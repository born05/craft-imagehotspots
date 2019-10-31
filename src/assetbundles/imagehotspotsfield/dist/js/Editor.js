/** global: Born05 */
/** global: Craft */
/** global: Garnish */

if (typeof Born05 === 'undefined') var Born05 = {};

Born05.ImageHotspotEditor = Garnish.Modal.extend({
    $img: null,
    $cancelBtn: null,
    $imgContainer: null,

    asset: null,
    pos: null,
    listData: null,

    init: function(asset, pos, listData, settings) {
        this.setSettings(settings, Born05.ImageHotspotEditor.defaults);

        this.asset = asset;
        this.pos = pos;
        this.updateData(listData);

        // Build the modal
        var $container = $('<div class="modal image-hotspot-editor"></div>').appendTo(Garnish.$bod);
        var $body = $('<div class="body"></div>').appendTo($container);
        var $footer = $('<div class="footer"/>').appendTo($container);

        this.$primaryButtons = $('<div class="buttons right"/>').appendTo($footer);
        this.$cancelBtn = $('<div class="btn submit">' + Craft.t('app', 'Done') + '</div>').appendTo(this.$primaryButtons);

        this.$imgContainer = $('<div class="image-container"></div>').appendTo($body);
        this.$img = $('<img src="'+this.asset.url+'" alt="" />').appendTo(this.$imgContainer);
        this.$point = $('<div class="current"></div>').appendTo(this.$imgContainer);
        this.setPoint(this.pos.x, this.pos.y);

        this.base($container, this.settings);

        this.addListener(this.$imgContainer, 'click', 'select');
        this.addListener(this.$cancelBtn, 'activate', 'hide');
    },

    createImage: function(asset) {
        this.$imgContainer.removeClass('align-height align-width');

        var containerBounds = this.$imgContainer[0].getBoundingClientRect();
        var containerRatio = containerBounds.width / containerBounds.height;
        var imageRatio = asset.width / asset.height;

        if (containerRatio > imageRatio) {
            this.$imgContainer.addClass('align-height');
        } else  {
            this.$imgContainer.addClass('align-width');
        }
    },

    updateData: function(listData) {
        this.listData = listData;
    },

    show: function() {
        Garnish.Modal.prototype.show.call(this);

        this.$imgContainer.find('.point').remove();

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

    onFadeIn: function() {
        this.createImage(this.asset);
    },

    setPoint: function(x, y) {
        this.$point.css({
            top: (y * 100) + '%',
            left: (x * 100) + '%',
        });
    },

    select: function(e) {
        e.preventDefault();

        var imageBounds = this.$img[0].getBoundingClientRect();

        var x = (e.clientX - imageBounds.left) / imageBounds.width;
        var y = (e.clientY - imageBounds.top) / imageBounds.height;
        this.setPoint(x, y);
        this.settings.onSelect(x, y);
    },
},
{
    defaults: {
        id: null,
        onSelect: $.noop,
    }
});
