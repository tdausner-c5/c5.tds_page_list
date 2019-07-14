<?php
/**
 * Page List Preview controller.
 *
 * the Page List Preview controller URL is
 *
 *	/ccm/tds_page_list/preview  => public function preview()
 *
 */
defined('C5_EXECUTE') or die('Access Denied.');

namespace Concrete\Package\TdsPageList\Controller;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\Request;
use Concrete\Package\TdsPageList\Block\TdsPageList\Controller;

class Preview extends AbstractController
{
    public function preview()
    {
        $request = Request::getInstance();
        $rq = $request->request();
        $request->setCurrentPage(\Page::getByID($rq['current_page']));
        $controller = new Controller();

        $rq['num'] = ($rq['num'] > 0) ? $rq['num'] : 0;
        $rq['cThis'] = ($rq['cParentID'] == $rq['current_page']) ? '1' : '0';
        $rq['cParentID'] = ($rq['cParentID'] == 'OTHER') ? $rq['cParentIDValue'] : $rq['cParentID'];

        if ($rq['filterDateOption'] != 'between') {
            $rq['filterDateStart'] = null;
            $rq['filterDateEnd'] = null;
        }

        if ($rq['filterDateOption'] == 'past') {
            $rq['filterDateDays'] = $rq['filterDatePast'];
        } elseif ($rq['filterDateOption'] == 'future') {
            $rq['filterDateDays'] = $rq['filterDateFuture'];
        } else {
            $rq['filterDateDays'] = null;
        }

        $controller->num = $rq['num'];
        $controller->cParentID = $rq['cParentID'];
        $controller->cThis = $rq['cThis'];
        $controller->orderBy = $rq['orderBy'];
        $controller->ptID = $rq['ptID'];
        $controller->rss = $rq['rss'];
        $controller->displayFeaturedOnly = $rq['displayFeaturedOnly'];
        $controller->displayAliases = $rq['displayAliases'];
        $controller->paginate = (bool) $rq['paginate'];
        $controller->enableExternalFiltering = $rq['enableExternalFiltering'];
        $controller->filterByRelated = $rq['filterByRelated'];
        $controller->relatedTopicAttributeKeyHandle = $rq['relatedTopicAttributeKeyHandle'];
        $controller->filterByCustomTopic = ($rq['topicFilter'] == 'custom') ? '1' : '0';
        $controller->customTopicAttributeKeyHandle = $rq['customTopicAttributeKeyHandle'];
        $controller->customTopicTreeNodeID = $rq['customTopicTreeNodeID'];
        $controller->includeAllDescendents = $rq['includeAllDescendents'];
        $controller->displayResults = $rq['displayResults'];
        $controller->includeDescription = $rq['includeDescription'];
        $controller->truncateSummaries = $rq['truncateSummaries'];
        $controller->truncateChars = $rq['truncateChars'];
        $controller->filterDateOption = $rq['filterDateOption'];
        $controller->filterDateStart = $rq['filterDateStart'];
        $controller->filterDateEnd = $rq['filterDateEnd'];
        $controller->filterDateDays = $rq['filterDateDays'];

        $controller->on_start();
        $controller->add();
        $controller->view();

        extract($controller->getSets());

        $includeName = $rq['includeName'];
        $pageNameClickable = $rq['pageNameClickable'];
        $nameFormat = $rq['nameFormat'];
        $includeDate = $rq['includeDate'];
        $datePos = $rq['datePos'];
        $firstBlockOrg = $rq['firstBlockOrg'];
        $displayThumbnail = $rq['displayThumbnail'];
        $thumbnailPos = $rq['thumbnailPos'];
        $thumbnailClickable = $rq['thumbnailClickable'];
        $useButtonForLink = $rq['useButtonForLink'];
        $buttonLinkText = $rq['buttonLinkText'];
        $pageListTitle = $rq['pageListTitle'];
        $noResultsMessage = $rq['noResultsMessage'];

        $pages = $controller->get('pages');

        require dirname(__FILE__) . '/../blocks/tds_page_list/view.php';
    }
    
    public function getViewObject()
    {
    }
}


