{**
* 2010-2014 EcomiZ
*
*  @author    EcomiZ
*  @copyright 2010-2014
*  @version  Release: 1.1 $Revision: 1 $
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{extends file=$layout}
{block name='content'}
    
    
{capture name=path}{$title}{/capture}

<h1>{$title|escape:'html'}</h1>

<!-- Module HomeSlider -->
<div id="homepage-slider">

    <ul id="homeslider">
        {foreach from=$data item=article}

            <li class="homeslider-container">
                <div class="view view-third ">
                    <img alt="" src="{$article['path_article']|escape:'html'}">

                    <div class="mask" style="width:{$width|escape:'html'}px; height:{$height|escape:'html'}px">
                        <div style="display:{if $display_legend == 0 }none{else}block{/if}">
                            <h2>{$article['name_article']|escape:'html'}</h2>
                            <h3>{$article['date_article']|date_format:"%d/%m/%Y"}</h3>
                            <p>{$article['desc_article']|escape:'html'}</p>
                        </div>
                        <a class="zoom fancybox" data-fancybox-group="other-views" href="{$article['path_article']|escape:'html'}">Zoom</a>
                    </div>
                </div>
            </li>

        {/foreach}
    </ul>

</div>
<!-- /Module HomeSlider -->
{/block}