{if isset($manufacturers) && $manufacturers}
    <div class="tz-man-slider">
        <!-- Manufacturers list -->
        <div id="owl-man-slider" class="owl-carousel">
            {foreach from=$manufacturers item=manufacturer name=manufacturers}

                    <a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
                        <img src="{$img_manu_dir}{$manufacturer.id_manufacturer|escape:'html':'UTF-8'}-medium_default.jpg" alt="" width="{$manSize.width}" height="{$manSize.height}" />
                    </a>
            {/foreach}
        </div>


    <script>
        $(document).ready(function() {
            var m_owl = $("#owl-man-slider");
            m_owl.owlCarousel({
                items : {$items_wide}, //10 items above 1000px browser width
                itemsDesktop : [1000,{$items_desktop}], //5 items between 1000px and 901px
                itemsDesktopSmall : [900,{$items_desktop_small}], // 3 items betweem 900px and 601px
                itemsTablet: [600,{$items_tablet}], //2 items between 600 and 0;
                itemsMobile : {$items_mobile}, // itemsMobile disabled - inherit from itemsTablet option
                autoPlay: {$tzc_autoplay},
                navigation: {$tzc_nav},
                navigationText: false,
                pagination: false
            });
        });
    </script>
    </div>
{/if}