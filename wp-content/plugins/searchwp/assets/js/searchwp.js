jQuery(document).ready(function($){

    var uniqid = function (prefix, more_entropy) {
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +    revised by: Kankrelune (http://www.webfaktory.info/)
        // %        note 1: Uses an internal counter (in php_js global) to avoid collision
        // *     example 1: uniqid();
        // *     returns 1: 'a30285b160c14'
        // *     example 2: uniqid('foo');
        // *     returns 2: 'fooa30285b1cd361'
        // *     example 3: uniqid('bar', true);
        // *     returns 3: 'bara20285b23dfd1.31879087'
        if (typeof prefix === 'undefined') {
            prefix = "";
        }

        var retId;
        var formatSeed = function (seed, reqWidth) {
            seed = parseInt(seed, 10).toString(16); // to hex str
            if (reqWidth < seed.length) { // so long we split
                return seed.slice(seed.length - reqWidth);
            }
            if (reqWidth > seed.length) { // so short we pad
                return new Array(1 + (reqWidth - seed.length)).join('0') + seed;
            }
            return seed;
        };

        // BEGIN REDUNDANT
        if (!this.php_js) {
            this.php_js = {};
        }
        // END REDUNDANT
        if (!this.php_js.uniqidSeed) { // init seed with big random int
            this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
        }
        this.php_js.uniqidSeed++;

        retId = prefix; // start with prefix, add current milliseconds hex string
        retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
        retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
        if (more_entropy) {
            // for more entropy we add a float lower to 10
            retId += (Math.random() * 10).toFixed(8).toString();
        }

        return retId;
    };

    var updateTabContentHeights = function( $parent ){
        // make sure our tab content is at least the proper height
        // while doing that, hide each tab pane
        var tallest = 0;
        $parent.find('.swp-tab-content .swp-tab-pane').each(function(){
            if($(this).height()>tallest){
                tallest = $(this).height();
            }
        });
        $parent.find('.swp-tab-content').height(tallest+20);
    };

    $(document).tooltip({
        items: ".swp-tooltip",
        content: function(){
            return $($(this).attr('href')).html();
        }
    });

    var excludeSelects = function() {
        $('select.swp-exclude-select').each(function(){
            $(this).select2({
                placeholder: $(this).data('placeholder')
            });
        });
    };

    excludeSelects();

    var initTabs = function( $grandparent ){
        $grandparent .find('.swp-tabbable').each(function(){

            var $parent = $(this);

            // prevent clicking labels from toggling the checkbox
            $parent.find('.swp-tabs label').unbind('click').click(function(e){
                e.preventDefault();
            });

            updateTabContentHeights($parent);
            $parent.find('.swp-tab-content .swp-tab-pane').hide();

            // hook the clicks
            $parent.find('.swp-tabs > li').click(function(){
                $parent.find('.swp-tabs > li.swp-tab-active').removeClass('swp-tab-active');
                $parent.find('.swp-tab-content .swp-tab-pane').hide();
                $(this).addClass('swp-tab-active');
                $('#'+$(this).data('swp-engine')).show();
            });

            // make sure the first tab is active
            if(!$parent.find('.swp-tabs .swp-tab-active').length){
                $parent.find('.swp-tabs > li:eq(0)').trigger('click');
            }

        });
    };

    initTabs( $('.swp-default-engine') );
    $('.swp-supplemental-engine').each(function(){
        initTabs( $(this) );
    });

    // allow addition of custom fields
    $('body').on('click','a.swp-add-custom-field', function(){
        _.templateSettings = {
            variable : 'swp',
            interpolate : /\{\{(.+?)\}\}/g
        };

        var template = _.template($('script#tmpl-swp-custom-fields').html());

        var swp = {
            arrayFlag: uniqid( 'swp' ),
            postType: $(this).data('posttype'),
            engine: $(this).data('engine')
        };

        $(this).parents('tbody').find('tr:last').before(template(swp));
        updateTabContentHeights($(this).parents('.swp-tabbable'));

        return false;
    });

    var $body = $('body');

    $body.on('click','.swp-delete',function(){
        $(this).parents('tr').remove();
        return false;
    });

    $body.on('click','.swp-supplemental-engine-edit-trigger',function(){
        $(this).parents('.swp-supplemental-engine').addClass('swp-supplemental-engine-edit');
        updateTabContentHeights($(this).parents('.swp-supplemental-engine'));
        return false;
    });

    $body.on('click','.swp-del-supplemental-engine',function(){
        $(this).parents('.swp-supplemental-engine').remove();
        return false;
    });

    $('.swp-add-supplemental-engine').click(function(){
        _.templateSettings = {
            variable : 'swp',
            interpolate : /\{\{(.+?)\}\}/g
        };

        var engineSettingsTemplate = _.template($('script#tmpl-swp-engine').html());
        var supplementalTemplate = _.template($('script#tmpl-swp-supplemental-engine').html());

        var swp = {
            engine: uniqid( 'swpengine' ),
            engineLabel: 'Supplemental'
        };

        swp.engineSettings = engineSettingsTemplate(swp);

        $(this).parents('.swp-supplemental-engines-wrapper').find('.swp-supplemental-engines').append(supplementalTemplate(swp));
        $(this).parents('.swp-supplemental-engines-wrapper').find('.swp-supplemental-engines .swp-supplemental-engine:last .swp-supplemental-engine-name > a').trigger('click');
        $(this).parents('.swp-supplemental-engines-wrapper').find('.swp-supplemental-engines .swp-supplemental-engine:last .swp-supplemental-engine-name > input').focus();
        initTabs( $('.swp-supplemental-engines .swp-supplemental-engine:last' ) );
        excludeSelects();
        return false;
    });



});
