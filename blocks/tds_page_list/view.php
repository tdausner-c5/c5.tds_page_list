<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Page;

$c = Page::getCurrentPage();
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

/** @var \Concrete\Core\Utility\Service\Text $th */
$th = $app->make('helper/text');
/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = $app->make('helper/date');

if ($c->isEditMode() && $controller->isBlockEmpty())
{
    ?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Page List Block.') ?></div>
    <?php
} else
{
    ?>

    <div class="ccm-block-page-list-wrapper">

        <?php if (isset($pageListTitle) && $pageListTitle)
        {
            ?>
            <div class="ccm-block-page-list-header">
                <h5><?php echo h($pageListTitle) ?></h5>
            </div>
            <?php
        } ?>

        <?php if (isset($rssUrl) && $rssUrl)
        {
            ?>
            <a href="<?php echo $rssUrl ?>" target="_blank" class="ccm-block-page-list-rss-feed">
                <i class="fa fa-rss"></i>
            </a>
            <?php
        } ?>

        <div class="ccm-block-page-list-pages">

            <?php
            $i = 0;
            foreach ($pages as $page)
            {

                // Prepare data for each page being listed...
                $title = $page->getCollectionName();
                if ($page->getCollectionPointerExternalLink() != '')
                {
                    $url = $page->getCollectionPointerExternalLink();
                    if ($page->openCollectionPointerExternalLinkInNewWindow())
                    {
                        $target = '_blank';
                    }
                } else
                {
                    $url = $page->getCollectionLink();
                    $target = $page->getAttribute('nav_target');
                }
                $target = empty($target) ? '_self' : $target;
                $description = $page->getCollectionDescription();
                $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;

                $thumbnail = false;
                if ($displayThumbnail > 0)
                {
                    $thumbnail = $page->getAttribute('thumbnail');
                }

                $date = false;
                if ($includeDate !== 'no')
                {
                    $date = $includeDate == 'datetime' ? $dh->formatDateTime($page->getCollectionDatePublic(), true)
                        : $dh->formatDate($page->getCollectionDatePublic(), true);
                }
                //Other useful page data...

                //$last_edited_by = $page->getVersionObject()->getVersionAuthorUserName();

                /* DISPLAY PAGE OWNER NAME
                 * $page_owner = UserInfo::getByID($page->getCollectionUserID());
                 * if (is_object($page_owner)) {
                 *     echo $page_owner->getUserDisplayName();
                 * }
                 */

                /* CUSTOM ATTRIBUTE EXAMPLES:
                 * $example_value = $page->getAttribute('example_attribute_handle', 'display');
                 *
                 * When you need the raw attribute value or object:
                 * $example_value = $page->getAttribute('example_attribute_handle');
                 */

                /* End data preparation. */

                /* The HTML from here through "endforeach" is repeated for every item in the list... */

                $thRender = '';
                $thumbTag = '';
                if (is_object($thumbnail))
                {
                    $img = $app->make('html/image', [$thumbnail]);
                    $tag = $img->getTag();
                    $tag->addClass('img-responsive');
                    $thRender = 'first';
                    switch ($thumbnailPos)
                    {
                        case 'left':
                            break;
                        case 'right':
                            $thRender = 'last';
                            break;
                        case 'random':
                            $thRender = random_int(0, 1) == 0 ? 'first' : 'last';
                        case 'alt-left':
                            $thRender = ($i % 2) == 0 ? 'first' : 'last';
                            break;
                        case 'alt-right':
                            $thRender = ($i % 2) != 0 ? 'first' : 'last';
                            break;
                    }
                    $thumbTag = $tag;
                    if (isset($thumbnailClickable) && $thumbnailClickable)
                    {
                        $thumbTag = '<a href="' . h($url) . '" target="' . h($target) . '">' . $tag . '</a>';
                    }
                }
                $hOpen = $hClose = '';
                $titleStr = '';
                $dateStr = '';
                $titleBlock = '';
                if (isset($includeName) && $includeName > 0)
                {
                    if (isset($nameFormat) && $nameFormat !== '')
                    {
                        $hOpen = '<' . $nameFormat . '>';
                        $hClose = '</' . $nameFormat . '>';
                    }
                    if (isset($pageNameClickable) && $pageNameClickable)
                    {
                        $hOpen = $hOpen . '<a href="' . h($url) . '" target="' . h($target) . '">';
                        $hClose = '</a>' . $he;
                    }
                    $titleStr = h($title);
                }
                if (isset($includeDate) && $includeDate !== 'no')
                {
                    if ($titleStr !== '')
                    {
                        $tag = ($datePos === 'over' || $datePos === 'below') ? 'div' : 'span';
                        $dateStr = '<' . $tag . ' class="ccm-block-page-list-date ' . $datePos . '">' . h($date) . '</' . $tag . '>';
                        $tOpen = '<div class="ccm-block-page-list-title">';
                        $tClose = '</div>';
                        $tBl = $tOpen . $hOpen . $titleStr . $hClose . $tClose;
                        switch ($datePos)
                        {
                            case 'over':
                                $titleBlock = $dateStr . $tBl;
                                break;
                            case 'after':
                                $titleBlock = $tOpen . $hOpen . $titleStr . $dateStr . $hClose . $tClose;
                                break;
                            case 'below':
                                $titleBlock = $tBl . $dateStr;
                                break;
                            case 'before':
                                $titleBlock = $tOpen . $hOpen . $dateStr . $titleStr . $hClose . $tClose;
                                break;
                        }
                    } else
                    {
                        $titleBlock = '<div class="ccm-block-page-list-date">' . h($date) . '</div>';
                    }
                } else
                {
                    if ($titleStr !== '')
                    {
                        $titleBlock = '<div class="ccm-block-page-list-title">' . $hOpen . $titleStr . $hClose . '</div>';
                    }
                }

                if ($firstBlockOrg && $i === 0)
                {
                    /*
                     * output
                     */
                    ?>
                    <div class="ccm-block-page-list-orgblock"><?php

                        echo $titleBlock;
                        $coll = $page->getByID($page->cID);
                        foreach ($coll->getBlocks('Main') as $block)
                        {
                            $block->display();
                        }
                        ?></div>
                    <div class="clearfix"></div><?php

                } else
                {
                    $msgStr = '';
                    if (isset($includeDescription) && $includeDescription)
                    {
                        $msgStr = '<div class="ccm-block-page-list-description">' . h($description) . '</div>';
                    }
                    if (isset($useButtonForLink) && $useButtonForLink > 0
                        && isset($buttonLinkText) && $buttonLinkText != '')
                    {
                        $aClass = $useButtonForLink < 2 ? "ccm-block-page-list-read-more"
                            : "btn btn-primary";
                        $msgStr .= '<div class="ccm-block-page-list-page-entry-read-more">
										<a href="' . h($url) . '" target="' . h($target) . '"
										   class="' . $aClass . '">' . h($buttonLinkText) . '</a>
									</div>';
                    }
                    /*
                     * output
                     */
                    ?>
                    <div class="row ccm-block-page-list-page-entry"><?php
                    if (isset($includeName) && $includeName > 1)
                    {
                        echo '<div class="col-xs-12">' . $titleBlock . '</div>';
                    } else
                    {
                        $msgStr = $titleBlock . $msgStr;
                    }

                    $msgClass = 'col-xs-12 col-sm-12';
                    if ($thRender !== '')
                    {
                        $thumbOffs = $titleOffs = '';
                        if ($thumbnailMobile && $thRender === 'last')
                        {
                            $thumbOffs = ' col-sm-push-' . (12 - $displayThumbnail);
                            $titleOffs = ' col-sm-pull-' . $displayThumbnail;
                            $thRender = 'first';
                        }
                        $thumbStr = '<div class="col-sm-' . $displayThumbnail . $thumbOffs . '">' . $thumbTag . '</div>';
                        $msgClass = 'col-sm-' . (12 - $displayThumbnail) . $titleOffs;
                    }

                    if ($thRender === 'first')
                    {
                        echo $thumbStr;
                    }
                    if ($msgStr != '')
                    {
                        echo
                            '<div class="' . $msgClass . '">'
                            . $msgStr .
                            '</div>';
                    }
                    if ($thRender === 'last')
                    {
                        echo $thumbStr;
                    }
                    ?></div><?php
                }
                $i++;
            } ?>
        </div><!-- end .ccm-block-page-list-pages -->

        <?php if (count($pages) == 0) { ?>
            <div class="ccm-block-page-list-no-pages"><?php echo h($noResultsMessage) ?></div>
        <?php } ?>

    </div><!-- end .ccm-block-page-list-wrapper -->


    <?php
    if ($showPagination)
    {
        echo $pagination;
    }

} ?>
