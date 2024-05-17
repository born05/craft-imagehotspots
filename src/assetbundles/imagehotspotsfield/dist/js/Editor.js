/** global: Born05 */
/** global: Craft */
/** global: Garnish */

if (typeof Born05 === 'undefined') var Born05 = {};

Born05.ImageHotspotEditor = Garnish.Modal.extend({
    $body: null,
    $footer: null,
    $buttons: null,

    $img: null,
    $saveBtn: null,
    $imgContainer: null,

    asset: null,
    pos: null,
    listData: null,
    imageBounds: null,
    
    isDraggingImage: false,
    isDraggingPoint: false,
    previousMouseX: 0,
    previousMouseY: 0,

    prevOffsetX: 0,
    prevOffsetY: 0,

    init: function(asset, pos, listData, settings) {
        this.setSettings(settings, Born05.ImageHotspotEditor.defaults);

        this.asset = asset;
        this.pos = pos;
        this.updateData(listData);

        // Build the modal
        this.$container = $('<div class="modal fitted image-hotspot-editor"></div>').appendTo(Garnish.$bod);
        this.$body = $('<div class="body"></div>').appendTo(this.$container);
        this.$footer = $('<div class="footer"/>').appendTo(this.$container);
        this.$buttons = $('<div class="buttons right"/>').appendTo(this.$footer);
        
        this.$imgContainer = $('<div class="image-hotspot-container"></div>').appendTo(this.$body);
        this.$primaryButtons = $('<div class="image-hotspot-buttons"/>').appendTo(this.$body);
        this.$saveBtn = $('<div class="btn submit">' + Craft.t('app', 'Done') + '</div>').appendTo(this.$buttons);
        
        this.base(this.$container, this.settings);
        this.updateSizeAndPosition();

        this.$img = $('<img src="'+this.asset.url+'" width="'+this.asset.width+'" height="'+this.asset.height+'" alt="" />').appendTo(this.$imgContainer);
        this.$point = $('<div class="current"></div>').appendTo(this.$imgContainer);
        this.setPoint(this.pos.x, this.pos.y);

        this.addListener(this.$saveBtn, 'activate', 'hide');
        
        // Add mouse event listeners
        this.addListener(this.$container, 'wheel', 'zoomImage');
 
        this.addListener(
            this.$container,
            'mousedown,touchstart',
            'handleDragStart'
        );
        this.addListener(
            this.$container,
            'mousemove,touchmove',
            'handleDragMove'
        );
        this.addListener(
            this.$container,
            'mouseup,touchend,touchcancel',
            'handleDragEnd'
        );
    },

    /**
     * Update the modal size and position on browser resize
     */
    updateSizeAndPosition: function () {
        if (!this.$container) {
            return;
        }

        // Fullscreen modal
        var innerWidth = window.innerWidth;
        var innerHeight = window.innerHeight;

        this.$container.css({
            width: innerWidth,
            'min-width': innerWidth,
            left: 0,

            height: innerHeight,
            'min-height': innerHeight,
            top: 0,
        });

        this.$body.css({
            height: innerHeight - (this.$footer.outerHeight() - 1),
        });

        if (innerWidth < innerHeight) {
            this.$container.addClass('vertical');
        } else {
            this.$container.removeClass('vertical');
        }

        // If image is already loaded, make sure it looks pretty.
        if (this.$imgContainer) {
            this.resizeImageContainer();
        }
    },


    /**
     * Make sure underlying content is not scrolled by accident.
     */
    onShow: function() {
        Garnish.$bod.addClass('no-scroll');

        // Loop through points
        Object.keys(this.listData).forEach(function(key) {
            if (key === this.settings.id) return;

            var $point = $('<div class="point"></div>').appendTo(this.$imgContainer);
            $point.css({
                top: (this.listData[key].y * 100) + '%',
                left: (this.listData[key].x * 100) + '%',
            });
        }, this);
    },

    /**
     * Allow the content to scroll.
     */
    onHide: function () {
        this.$imgContainer.find('.point').remove();

        Garnish.$bod.removeClass('no-scroll');
    },

    resizeImageContainer: function() {
        this.containerBounds = this.$body[0].getBoundingClientRect();
        this.containerRatio = this.containerBounds.width / this.containerBounds.height;
        this.imageRatio = this.asset.width / this.asset.height;
        this.zoom = 1;
        this.offsetX = 0;
        this.offsetY = 0;

        if (this.containerRatio > this.imageRatio) {
            this.$imgContainer.css({
                width: this.asset.width * (this.containerBounds.height / this.asset.height),
                height: this.containerBounds.height,
                top: 0,
                left: (this.containerBounds.width - (this.asset.width * (this.containerBounds.height / this.asset.height))) / 2,
                transform: 'translate(0, 0)',
                opacity: 1,
            });
        } else  {
            this.$imgContainer.css({
                width: this.containerBounds.width,
                height: this.asset.height * (this.containerBounds.width / this.asset.width),
                top: (this.containerBounds.height - (this.asset.height * (this.containerBounds.width / this.asset.width))) / 2,
                left: 0,
                transform: 'translate(0, 0)',
                opacity: 1,
            });
        }
    },

    zoomImage: function(e) {
        this.zoom = Math.max(1, Math.min(20, this.zoom + e.originalEvent.deltaY/-100));
        this.updateTransform();
    },

    updateTransform: function() {
        this.$imgContainer.css({
            transform: 'translate('+this.offsetX+'px, '+this.offsetY+'px) scale('+this.zoom+')',
        });
    },

    handleDragStart: function(e) {
        if (this.isDraggingPoint) return;
        e.preventDefault();

        if (e.target === this.$point[0]) {
            this.imageBounds = this.$img[0].getBoundingClientRect();
            this.isDraggingPoint = true;
        } else {
            this.previousMouseX = e.pageX;
            this.previousMouseY = e.pageY;
            this.prevOffsetX = this.offsetX;
            this.prevOffsetY = this.offsetY;
            this.isDraggingImage = true;
        }
    },
    handleDragMove: function(e) {
        if (!this.isDraggingImage && !this.isDraggingPoint) return;
        e.preventDefault();

        if (this.isDraggingImage) {
            this.offsetX = this.prevOffsetX + (e.pageX - this.previousMouseX);
            this.offsetY = this.prevOffsetY + (e.pageY - this.previousMouseY);
            this.updateTransform();
        }

        if (this.isDraggingPoint) {
            var x = (e.clientX - this.imageBounds.left) / this.imageBounds.width;
            var y = (e.clientY - this.imageBounds.top) / this.imageBounds.height;
            this.setPoint(x, y);
        }
    },
    handleDragEnd: function(e) {
        if (!this.isDraggingImage && !this.isDraggingPoint) return;
        e.preventDefault();

        this.isDraggingImage = false;
        this.isDraggingPoint = false;
    },

    updateData: function(listData) {
        this.listData = listData;
    },

    setPoint: function(x, y) {
        this.$point.css({
            top: (y * 100) + '%',
            left: (x * 100) + '%',
        });
        this.settings.onSelect(x, y);
    },
},
{
    defaults: {
        animationDuration: 100,
    }
});
