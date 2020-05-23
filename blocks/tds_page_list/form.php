<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $attributeKeys ArrayObject
 * @var $bt BlockType
 * @var $featuredAttribute CollectionAttributeKey
 * @var $thumbnailAttribute CollectionAttributeKey
 * @var $c Concrete\Core\Page\Page 
 * @var $cThis boolean
 * @var $cThisParent boolean
 * @var $displayAliases boolean
 * @var $displayFeaturedOnly boolean
 * @var $enableExternalFiltering boolean
 * @var $filterByCustomTopic boolean
 * @var $filterByRelated boolean
 * @var $firstBlockOrg boolean
 * @var $ignorePermissions boolean
 * @var $includeAllDescendents boolean
 * @var $includeDescription boolean
 * @var $isOtherPage boolean
 * @var $pageNameClickable boolean
 * @var $thumbnailClickable boolean
 * @var $thumbnailMobile boolean
 * @var $truncateSummaries boolean
 * @var $displayThumbnail integer
 * @var $includeName integer
 * @var $num integer
 * @var $paginate integer
 * @var $ptID integer
 * @var $start integer
 * @var $truncateChars integer
 * @var $useButtonForLink integer
 * @var $buttonLinkText string
 * @var $datePos string
 * @var $displayResults string
 * @var $filterDateDays string
 * @var $filterDateEnd string
 * @var $filterDateOption string
 * @var $filterDateStart string
 * @var $includeDate string
 * @var $nameFormat string
 * @var $noResultsMessage string
 * @var $orderBy string
 * @var $pageListTitle string
 * @var $relatedTopicAttributeKeyHandle string
 * @var $rssFeed string
 * @var $thumbnailPos string
 * @var $topicFilter string
 */

use Concrete\Package\TdsPageList\Controller\Form;
use Concrete\Core\Support\Facade\Facade;

$c = Concrete\Core\Page\Page::getCurrentPage();
$siteType = null;
if ($c)
{
    $pageType = $c->getPageTypeObject();
    if ($pageType)
    {
        $siteType = $pageType->getSiteTypeObject(); // gotta have this for editing defaults pages.
    }
    else
    {
        $tree = $c->getSiteTreeObject();
        if (is_object($tree))
        {
            $siteType = $tree->getSiteType();
        }
    }
}
$app = Facade::getFacadeApplication();
$selector = $app->make('helper/form/page_selector');
$form = new Form($app);

echo $app->make('helper/concrete/ui')->tabs([
    ['page-list-sampling', t('Sampling'), true],
    ['page-list-output', t('Output')],
    ['page-list-preview', t('Preview')]
]);?>

<div class="ccm-tab-content" id="ccm-tab-content-page-list-sampling">
    <div class="pagelist-form">

        <fieldset>
            <div class="form-group">
                <?php
                $ctArray = PageType::getList(false, $siteType);
                if (is_array($ctArray))
                {
                    $pgTypes = [ 0 => '** ' . t('All') . ' **' ];
                    foreach ($ctArray as $ct)
                    {
                        $pgTypes += [
                            $ct->getPageTypeID() => $ct->getPageTypeDisplayName()
                        ];
                    }
                    echo
                    $form->label('ptID', t('Page Type')),
                    $form->select('ptID', $pgTypes, $ptID);
                }
                ?>
            </div>
            <div class="form-group"><?php echo
                $form->label('paginate', t('Pagination')),
                $form->checkboxList( [
                    'paginate' => [
                        'label'   => t('Display pagination interface if more items are available than are displayed.'),
                        'checked' => $paginate == 1,
                    ]
                ]);
                ?>
            </div>
            <div class="form-group"><?php echo
                $form->label('num', t('Number of pages to display')),
                $form->number('num', $num, ['min' => '0' ]);
                ?>
            </div>
            <div class="form-group start-page"><?php echo
                $form->label('start', t('Start page to display')),
                $form->number('start', $start, ['min' => '1' ]);
                ?>
            </div>
        </fieldset>

        <fieldset>
            <div class="form-group"><?php echo
                $form->label('topicFilter', t('Topics'), [ 'class' => 'radioLine-2' ]),
                $form->radioList('topicFilter', [
                    ''			=> t('No topic filtering'),
                    'custom'	=> t('Custom Topic'),
                    'related'	=> t('Related Topic'),
                ], [
                    'checked'	=> $topicFilter,
                    'class'		=> 'radio radioLine-2',
                ]);
                ?>
                <div data-row="custom-topic">
                    <select class="form-control" name="customTopicAttributeKeyHandle" id="customTopicAttributeKeyHandle">
                        <option value=""><?php echo t('Choose topics attribute.')?></option>
                        <?php
                        foreach ($attributeKeys as $attributeKey) {
                            $attributeController = $attributeKey->getController();
                            $selected = $attributeKey->getAttributeKeyHandle() == $customTopicAttributeKeyHandle ? ' selected' : '';
                            echo
                                '<option data-topic-tree-id="' . $attributeController->getTopicTreeID() . '" 
									value="' . $attributeKey->getAttributeKeyHandle() . '"' . $selected . '>'
                                . $attributeKey->getAttributeKeyDisplayName(). '</option>';
                        } ?>
                    </select>

                    <div class="tree-view-container">
                        <div class="tree-view-template">
                        </div>
                    </div>
                    <input type="hidden" name="customTopicTreeNodeID" value="<?php echo $customTopicTreeNodeID ?>">

                </div>
                <div data-row="related-topic">
                    <span class="help-block"><?php echo t('Allows other blocks like the topic list block to pass search criteria to this page list block.')?></span>
                    <?php
                    $attrs = [ '' => t('Choose topics attribute.') ];
                    foreach ($attributeKeys as $attributeKey)
                    {
                        $attrs += [
                            $attributeKey->getAttributeKeyHandle() => $attributeKey->getAttributeKeyDisplayName()
                        ];
                    }
                    echo $form->select('relatedTopicAttributeKeyHandle', $attrs, $relatedTopicAttributeKeyHandle);
                    ?>
                </div>
            </div>

        </fieldset>

        <fieldset>
            <div class="form-group"><?php echo
                $form->label('filterDateOption', t('Filter by Publishing Date'), [ 'class' => 'radioLine-3']),
                $form->radioList('filterDateOption', [
                    'all'		=> t('Show All'),
                    'now'		=> t('Today'),
                    'past'		=> t('Before Today'),
                    'future'	=> t('After Today'),
                    'between'	=> t('Between'),
                ], [
                    'checked'	=> $filterDateOption,
                    'class'		=> 'radio radioLine-3',
                    'inputClass'=> 'filterDateOption',
                ]);
                ?>
                <div class="filterDateOptionDetail" data-filterDateOption="past">
                    <div class="form-group">
                        <label class="control-label"><?php echo t('Days in the Past')?> <i class="launch-tooltip fa fa-question-circle" title="<?php echo t('Leave 0 to show all past dated pages')?>"></i></label>
                        <input type="text" name="filterDatePast" value="<?php echo $filterDateDays ?>" class="form-control">
                    </div>
                </div>

                <div class="filterDateOptionDetail" data-filterDateOption="future">
                    <div class="form-group">
                        <label class="control-label"><?php echo t('Days in the Future')?> <i class="launch-tooltip fa fa-question-circle" title="<?php echo t('Leave 0 to show all future dated pages')?>"></i></label>
                        <input type="text" name="filterDateFuture" value="<?php echo $filterDateDays ?>" class="form-control">
                    </div>
                </div>

                <div class="filterDateOptionDetail" data-filterDateOption="between">
                    <?php
                    $datetime = $app->make('helper/form/date_time');
                    echo $datetime->date('filterDateStart', $filterDateStart);
                    echo "<p>" . t('and') . "</p>";
                    echo $datetime->date('filterDateEnd', $filterDateEnd);
                    ?>
                </div>

            </div>

        </fieldset>

        <fieldset>
            <div class="form-group"><?php echo
                $form->label(null, t('Other Filters')),
                $form->checkboxList( [
                    'displayFeaturedOnly'	=> [
                        'label'		=> t('Featured pages only.'),
                        'disabled'	=> !is_object($featuredAttribute),
                        'help'		=> t('(<strong>Note</strong>: You must create the "is_featured" page attribute first.)'),
                        'checked'	=> $displayFeaturedOnly == 1,
                    ],
                    'displayAliases'		=> [
                        'label'		=> t('Display page aliases.'),
                        'checked'	=> $displayAliases == 1,
                    ],
                    'ignorePermissions'		=> [
                        'label'		=> t('Ignore page permissions.'),
                        'checked'	=> $ignorePermissions == 1,
                    ],
                    'enableExternalFiltering' => [
                        'label'		=> t('Enable Other Blocks to Filter This Page List.'),
                        'checked'	=> $enableExternalFiltering,
                    ],
                ], [
                    'class'		 => 'checkbox',
                    'inputClass' => 'otherFilters',
                ]);
                ?>
            </div>
        </fieldset>

        <fieldset>
            <div class="form-group"><?php echo
                $form->label('cParentID', t('Location'), [ 'class' => 'radioLine-2' ]),
                $form->radioList( 'cParentID', [
                    [
                        'id'		=> 'cEverywhereField',
                        'label'		=> t('Everywhere'),
                        'value'		=> 0,
                        'checked'	=> $cParentID == 0,
                    ],
                    [
                        'id'		=> 'cThisPageField',
                        'label'		=> t('Beneath this page'),
                        'value'		=> $c->getCollectionID(),
                        'checked'	=> $cThis,
                    ],
                    [
                        'id'		=> 'cThisParentField',
                        'label'		=> t('At the current level'),
                        'value'		=> $c->getCollectionParentID(),
                        'checked'	=> $cThisParent,
                    ],
                    [
                        'id'		=> 'cOtherField',
                        'label'		=> t('Beneath another page'),
                        'value'		=> 'OTHER',
                        'checked'	=> $isOtherPage,
                    ],
                ], [ 'class' => 'radio radioLine-2' ] )
                ?>
                <div class="ccm-page-list-page-other" <?php if (!$isOtherPage) {
                    ?> style="display: none" <?php
                } ?>>

                    <?php echo $selector->selectPage('cParentIDValue', $isOtherPage ? $cParentID : false); ?>
                </div>

                <div class="ccm-page-list-all-descendents">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="includeAllDescendents" id="includeAllDescendents"
                                   value="1" <?php echo $includeAllDescendents ? 'checked="checked"' : '' ?> />
                            <?php echo t('Include all child pages') ?>
                        </label>
                    </div>
                </div>

            </div>

        </fieldset>

        <fieldset>
            <div class="form-group"><?php echo
                $form->label('orderBy', t('Sort')),
                $form->select('orderBy', [
                    'display_asc'	=> t('Sitemap order'),
                    'display_desc'	=> t('Reverse sitemap order'),
                    'chrono_desc'	=> t('Most recent first'),
                    'chrono_asc'	=> t('Earliest first'),
                    'alpha_asc'		=> t('Alphabetical order'),
                    'alpha_desc'	=> t('Reverse alphabetical order'),
                    'modified_desc'	=> t('Most recently modified first'),
                    'random'		=> t('Random'),
                ], $orderBy);
                ?>
            </div>
            <div class="ccm-page-list-display-results" <?php echo (strpos($orderBy, 'chrono') == 0 ? '' : ' style="display:none;"') ?>>
                <div class="form-group"><?php echo
                    $form->label('displayResults', t('Display of results'), [ 'class' => 'radioLine-3' ]),
                    $form->radioList('displayResults', [
                        'all'		=> t('Show All'),
                        'year'		=> t('Latest year'),
                        'yearMonth'	=> t('Latest year & month'),
                    ], [
                        'checked'	=> $displayResults,
                        'class'		=> 'radio radioLine-3',
                        'inputClass'=> 'displayResults',
                    ]);
                    ?>
                </div>
            </div>
        </fieldset>

    </div>
</div>

<div class="ccm-tab-content" id="ccm-tab-content-page-list-output">
    <div class="pagelist-form">

        <fieldset>
            <div class="form-group"><?php echo
                $form->label('pageListTitle', t('Title of Page List')),
                $form->text('pageListTitle', $pageListTitle)
                ?>
            </div>
            <div class="form-group"><?php echo
                $form->label('noResultsMessage', t('Message to Display When No Pages Listed.')),
                $form->textarea('noResultsMessage', $noResultsMessage)
                ?>
        </fieldset>

        <fieldset>

            <div class="form-group"><?php echo
                $form->label('firstBlockOrg', t('Display first entry from origin'), [ 'class' => 'radioLine-3']),
                $form->radioList('firstBlockOrg', [
                    0	=> t('No'),
                    1	=> t('Yes'),
                ], [
                    'checked'	=> $firstBlockOrg ? 1 : 0,
                    'class'		=> 'radio radioLine-3',
                ]);
                ?>
            </div>

        </fieldset>

        <fieldset>
            <div class="form-group"><?php echo
                $form->label('includeName', t('Include Page Name'), [ 'class' => 'radioLine-3']),
                $form->radioList('includeName', [
                    0	=> t('No'),
                    1	=> t('Yes'),
                    2	=> t('Full width'),
                ], [
                    'checked'	=> $includeName,
                    'class'		=> 'radio radioLine-3',
                ]);
                ?>
                <div class="ccm-page-list-show-name" <?php echo ($includeName > 0 ? "" : "style=\"display:none;\"") ?>>
                    <div class="checkbox ml10">
                        <label>
                            <input type="checkbox" name="pageNameClickable" id="pageNameClickable"
                                   value="1" <?php echo $pageNameClickable ? 'checked="checked"' : '' ?> />
                            <?php echo t('Page name clickable') ?>
                        </label>
                    </div>
                    <?php echo
                    $form->label('nameFormat', t('Format for Page Name'), [ 'class' => 'mt20 radioLine-3']),
                    $form->radioList('nameFormat', [
                        ''		=> t('None'),
                        'h2'	=> t('Header 2'),
                        'h3'	=> t('Header 3'),
                        'h4'	=> t('Header 4'),
                        'h5'	=> t('Header 5'),
                        'h6'	=> t('Header 6'),
                    ], [
                        'checked'	=> $nameFormat,
                        'class'		=> 'radio radioLine-3',
                    ]);
                    ?>
                </div>
                <?php echo
                $form->label('includeDate', t('Include Public Page Date'), [ 'class' => 'mt20 radioLine-3' ]),
                $form->radioList('includeDate', [
                    'no'		=> t('No'),
                    'date'		=> t('Date'),
                    'datetime'	=> t('Date, time'),
                ], [
                    'checked'	=> $includeDate,
                    'class'		=> 'radio radioLine-3',
                ]);
                ?>
                <div class="ccm-page-list-date-pos" <?php echo ($includeDate != 'no' ? "" : "style=\"display:none;\"") ?>>
                    <?php echo
                    $form->label('datePos', t('Position for date/time'), [ 'class' => 'mt20 radioLine-3']),
                    $form->radioList('datePos', [
                        'over'		=> t('Over page name'),
                        'after'		=> t('After page name'),
                        'below'		=> t('Below page name'),
                        'before'	=> t('Before page name'),
                    ], [
                        'checked'	=> $datePos,
                        'class'		=> 'radio radioLine-3',
                    ]);
                    ?>
                    <span class="help-block"><?php echo t('This is usually the date the page is created. It can be changed from the page attributes panel.')?></span>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <div class="form-group"><?php echo
                $form->label('includeDescription', t('Include Page Description'), [ 'class' => 'radioLine-3']),
                $form->radioList('includeDescription', [
                    0	=> t('No'),
                    1	=> t('Yes'),
                ], [
                    'checked'	=> $includeDescription ? 1 : 0,
                    'class'		=> 'radio radioLine-3',
                ]);
                ?>
                <div class="ccm-page-list-truncate-description" <?php echo ($includeDescription ? "" : "style=\"display:none;\"") ?>>
                    <?php echo $form->label('includeDescription', t('Display Truncated Description')) ?>
                    <div class="input-group">
						<span class="input-group-addon">
							<input id="ccm-pagelist-truncateSummariesOn" name="truncateSummaries" type="checkbox"
                                   value="1" <?php echo ($truncateSummaries ? "checked=\"checked\"" : "") ?> />
						</span>
                        <input class="form-control" id="ccm-pagelist-truncateChars" <?php echo ($truncateSummaries ? "" : "disabled=\"disabled\"") ?>
                               type="text" name="truncateChars" size="3" value="<?php echo intval($truncateChars) ?>" />
                        <span class="input-group-addon">
							<?php echo t('characters') ?>
						</span>
                    </div>
                </div>
            </div>

            <div class="form-group"><?php echo
                $form->label('displayThumbnail', t('Display Thumbnail Image'), [ 'class' => 'radioLine-3' ]);
                if (is_object($thumbnailAttribute))
                {
                    echo $form->select('displayThumbnail', [
                        0	=> t('No image'),
                        2	=> t('16.6%'),
                        3	=> t('25%'),
                        4	=> t('33.3%'),
                        5	=> t('41.6%'),
                        6	=> t('50%'),
                        7	=> t('58.3%'),
                        8	=> t('66.6%'),
                        9	=> t('75%'),
                        10	=> t('83.3%'),
                    ], $displayThumbnail);
                }
                else
                {
                    ?>
                    <div class="help-block">
                        <?php echo t('You must create an attribute with the \'thumbnail\' handle in order to use this option.')?>
                    </div>
                    <?php
                }
                ?>
                <div class="ccm-page-list-thumbnail-pos" <?php echo ($displayThumbnail > 0  ? "" : "style=\"display:none;\"") ?>>
                    <div class="form-group"><?php echo
                        $form->label('thumbnailPos', t('Thumbnail position'), [ 'class' => 'radioLine-3 mt10']),
                        $form->radioList('thumbnailPos', [
                            'left'		=> t('Left'),
                            'right'		=> t('Right'),
                            'random'	=> t('Random'),
                            'alt-left'	=> t('Alternating start left'),
                            'alt-right'	=> t('Alternating start right'),
                        ], [
                            'checked'	=> $thumbnailPos,
                            'class'		=> 'radio radioLine-3',
                        ]);
                        ?>
                        <div class="checkbox ml10">
                            <label>
                                <input type="checkbox" name="thumbnailClickable" id="thumbnailClickable"
                                       value="1" <?php echo $thumbnailClickable ? 'checked="checked"' : '' ?> />
                                <?php echo t('Thumbnail clickable') ?>
                            </label>
                        </div>
                        <div class="checkbox ml10">
                            <label>
                                <input type="checkbox" name="thumbnailMobile" id="thumbnailMobile"
                                       value="1" <?php echo $thumbnailMobile ? 'checked="checked"' : '' ?> />
                                <?php echo t('Thumbnail first (left) on mobile devices') ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <div class="form-group"><?php echo
                $form->label('useButtonForLink', t('Click Page Name and/or Link Text to go to entry'), [ 'class' => 'radioLine-3']),
                $form->radioList('useButtonForLink', [
                    0	=> t('No'),
                    1	=> t('Link Text'),
                    2	=> t('Button'),
                ], [
                    'checked'	=> $useButtonForLink,
                    'class'		=> 'radio radioLine-3',
                ]);
                ?>
                <div class="ccm-page-list-button-text" <?php echo ($useButtonForLink > 0 ? "" : "style=\"display:none;\"") ?>>
                    <div class="form-group"><?php echo
                        $form->label('buttonLinkText', t('Link/Button Text')),
                        $form->text('buttonLinkText', $buttonLinkText)
                        ?>
                    </div>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <div class="form-group"><?php echo
                $form->label('rss', t('Provide RSS Feed'), [ 'class' => 'radioLine-3']),
                $form->radioList('rss', [
                    0	=> t('No'),
                    1	=> t('Yes'),
                ], [
                    'checked'	=> is_object($rssFeed) ? 1 : 0,
                    'class'		=> 'radio radioLine-3',
                    'inputClass'=> 'rssSelector',
                ]);
                ?>

                <div id="ccm-pagelist-rssDetails" <?php echo (is_object($rssFeed) ? "" : "style=\"display:none;\"") ?>>
                    <?php if (is_object($rssFeed)) {
                        echo t('RSS Feed can be found here: <a href="%s" target="_blank">%s</a>', $rssFeed->getFeedURL(), $rssFeed->getFeedURL());
                    } else {
                        ?>
                        <div class="form-group mb10">
                            <label class="control-label"><?php echo t('RSS Feed Title') ?></label>
                            <input class="form-control" id="ccm-pagelist-rssTitle" type="text" name="rssTitle" value=""/>
                        </div>
                        <div class="form-group mb10">
                            <label class="control-label"><?php echo t('RSS Feed Description') ?></label>
                            <textarea name="rssDescription" class="form-control"></textarea>
                        </div>
                        <div class="form-group mb10">
                            <label class="control-label"><?php echo t('RSS Feed Address (URL)') ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo URL::to('/rss')?>/</span>
                                <input type="text" name="rssHandle" value="" />
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>

        </fieldset>

        <div class="loader">
            <i class="fa fa-cog fa-spin"></i>
        </div>

    </div>
</div>

<div class="ccm-tab-content" id="ccm-tab-content-page-list-preview">
    <div class="preview">
        <div class="render">

        </div>
    </div>
</div>

<style type="text/css">
    /* sampling */
    div[data-filterDateOption=between] div {
        display: inline-block;
        width: 45%;
    }
    div[data-filterDateOption=between] p {
        display: inline-flex;
        justify-content: center;
        width: 10%;
    }

    /* output */
    .ccm-ui label.control-label.radioLine-2,
    .ccm-ui label.control-label.radioLine-3 {
        display: block;
    }
    .ccm-ui div.radio.radioLine-2,
    .ccm-ui div.radio.radioLine-3 {
        display: inline-flex;
        margin-left: 10px;
        margin-top: 0 !important;
    }
    .ccm-ui div.radio.radioLine-2 {
        width: 48%;
    }
    .ccm-ui div.radio.radioLine-3 {
        width: 30%;
    }
    .mb10 { margin-bottom: 10px !important; }
    .mt10 { margin-top: 10px !important; }
    .mt20 { margin-top: 20px !important; }
    .ml10 { margin-left: 10px !important; margin-top: 0 !important; }

    div.render {
        position: relative;
    }
    div.cover {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 10;
    }

    .ccm-page-list-title {
        font-size: 12px;
        font-weight: normal;
    }

    div.pagelist-form label.checkbox,
    div.pagelist-form label.radio {
        font-weight: 300;
    }

</style>
<script type="application/javascript">
    Concrete.event.publish('pagelist.edit.open');
    $(function() {
        var treeViewTemplate = $('.tree-view-template');

        $('input[name=paginate]').on('change', function() {
           this.checked ? $('.form-group.start-page').slideUp() : $('.form-group.start-page').slideDown();
        });
        $('select[name=customTopicAttributeKeyHandle]').on('change', function() {
            var chosenTree = $(this).find('option:selected').attr('data-topic-tree-id');
            $('.tree-view-template').remove();
            if (!chosenTree) {
                return;
            }
            $('.tree-view-container').append(treeViewTemplate);
            $('.tree-view-template').concreteTree({
                'treeID': chosenTree,
                'chooseNodeInForm': true,
                'selectNodesByKey': [<?php echo intval($customTopicTreeNodeID)?>],
                'onSelect' : function(nodes) {
                    if (nodes.length) {
                        $('input[name=customTopicTreeNodeID]').val(nodes[0]);
                    } else {
                        $('input[name=customTopicTreeNodeID]').val('');
                    }
                    Concrete.event.publish('pagelist.topictree.select');
                }
            });
        });
        $('input[name=topicFilter]:checked').trigger('change');
        if ($('#topicFilterCustom').is(':checked')) {
            $('select[name=customTopicAttributeKeyHandle]').trigger('change');
        }
    });

</script>

