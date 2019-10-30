/** global: Born05 */
/** global: Craft */
/** global: Garnish */

if (typeof Born05 === 'undefined') var Born05 = {};

Born05.ImageHotspotData = {};

Born05.ImageHotspotInput = Garnish.Base.extend({
    $container: null,
    $xField: null,
    $yField: null,
    $editBtn: null,

    init: function(settings) {
        this.setSettings(settings, Born05.ImageHotspotInput.defaults);

        this.$container = this.getContainer();

        this.$editBtn = this.getEditBtn();

        this.$xField = this.$container.children('[data-imagehotspot-x]');
        this.$yField = this.$container.children('[data-imagehotspot-y]');

        if (this.$editBtn) {
            this.addListener(this.$editBtn, 'activate', 'showModal');
        }

        this.setListData(
            parseFloat(this.$xField.val() || 0.5),
            parseFloat(this.$yField.val() || 0.5)
        );
    },

    getContainer: function() {
        return $('#' + this.settings.id);
    },

    getEditBtn: function() {
        return this.$container.children('.btn.edit');
    },

    showModal: function() {
        if (!this.modal) {
            this.modal = this.createModal();
        }
        else {
            this.modal.updateData(this.getListData());
            this.modal.show();
        }
    },

    createModal: function() {
        return new Born05.ImageHotspotEditor(
            this.settings.assetUrl,
            {
                x: parseFloat(this.$xField.val() || 0.5),
                y: parseFloat(this.$yField.val() || 0.5),
            },
            this.getListData(),
            this.getModalSettings()
        );
    },

    getModalSettings: function() {
        var currentId = this.$container.attr('id');
        return $.extend({
            id: currentId,
            onSelect: $.proxy(this, 'onModalSelect')
        }, this.settings.modalSettings);
    },

    getListData: function() {
        if (!Born05.ImageHotspotData[this.$container.attr('data-imagehotspot')]) {
            Born05.ImageHotspotData[this.$container.attr('data-imagehotspot')] = {};
        }

        return Born05.ImageHotspotData[this.$container.attr('data-imagehotspot')];
    },

    setListData: function(x, y) {
        this.getListData(); // Ensures the list is available
        var currentId = this.$container.attr('id');
        Born05.ImageHotspotData[this.$container.attr('data-imagehotspot')][currentId] = {x, y};
    },

    onModalSelect: function(x, y) {
        this.$xField.val(x);
        this.$yField.val(y);

        this.setListData(x, y);
    }
}, {
    defaults: {
        modalSettings: {}
    }
});
