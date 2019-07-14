<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-date-navigation-wrapper">

    <div class="ccm-block-date-navigation-header">
        <h5><?php echo h($title)?></h5>
    </div>

    <?php if (count($dates)) {
		$aClass = '';
		if ($view->controller->isAllDate())
		{ 
			$aClass = ' class="ccm-block-date-navigation-date-selected"';
        }
		$allHtml = '<li><a href="' . $view->controller->getDateLink(). '"' . $aClass . '>' . t('All') . '</a></li>';
    ?>
        <ul class="ccm-block-date-navigation-dates">
			<?php
			if ( $allPosition == 0 )
			{
				echo $allHtml;
			}
            foreach ($dates as $date)
			{
				$aClass = '';
				if ($view->controller->isSelectedDate($date))
				{ 
					$aClass = ' class="ccm-block-date-navigation-date-selected"';
				}
				echo '<li><a href="' . $view->controller->getDateLink($date) . '"' . $aClass . '>' .
					$view->controller->getDateLabel($date) . '</a></li>';
			}
			if ( $allPosition == 1 )
			{
				echo $allHtml;
			}
		    ?>
        </ul>
	<?php } else {
		?>
			<?php echo t('None.')?>
		<?php 
	} ?>

</div>
