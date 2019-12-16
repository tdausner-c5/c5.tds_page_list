<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var integer $filterByParent
 * @var boolean $redirectToResults
 * @var integer $filterMode
 * @var integer $allPosition
 */
$pageSel = $app->make('helper/form/page_selector');
$form = new Application\Form\Service\Form($app);

?>
<fieldset>
    <legend><?php echo t('Filtering')?></legend>
    <div class="form-group">
		<?php echo $form->label(null, t('By Parent Page') . ':') ?>
        <div class="checkbox">
            <label>
                <?php echo
					$form->checkbox('filterByParent', $filterByParent, isset($cParentID) && (int) $cParentID > 0),
					t('Filter by Parent Page')
				?>
            </label>
        </div>
        <div id="ccm-block-related-pages-parent-page">
            <?php
	            echo $pageSel->selectPage('cParentID', isset($cParentID) ? $cParentID : null);
            ?>
        </div>
    </div>

    <div class="form-group"><?php
		$ptArr = [ 0 => '** ' . t('All') . ' **' ];
		foreach ($pagetypes as $ct)
		{
			$ptArr += [ $ct->getPageTypeID() => $ct->getPageTypeDisplayName() ];
		}
		echo 
		$form->label('ptID', t('By Page Type')),
		$form->select('ptID', $ptArr, $ct->getPageTypeID())
		?>
    </div>
</fieldset>
<fieldset>
    <legend><?php echo t("Results")?></legend>
    <div class="form-group">
        <div class="checkbox">
            <label>
				<?php echo
					$form->checkbox('redirectToResults', $redirectToResults, isset($cTargetID) && (int) $cTargetID > 0),
					t('Redirect to Different Page on Click')
                ?>
             </label>
        </div>
        <div id="ccm-block-related-pages-search-page">
            <?php
            echo $pageSel->selectPage('cTargetID', isset($cTargetID) ? $cTargetID : null);
            ?>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend><?php echo t('Formatting')?></legend>
    <div class="form-group"><?php echo
		$form->label('title', t('Title')),
		$form->text('title', $title)
		?>
    </div>
    <div class="form-group"><?php echo
		$form->label('filterMode', t('Filter mode'), [ 'class' => 'radioLine-2' ]),
		$form->radioList('filterMode', [
					0 => 'Month & Year',
					1 => 'Year',
				], $filterMode, [ 'class' => 'radio radioLine-2' ])
		?>
    </div>
    <div class="form-group"><?php echo
		$form->label('allPosition', t('Position of "all" entry'), [ 'class' => 'radioLine-2' ]),
		$form->radioList('allPosition', [
					0 => 'Start of list',
					1 => 'End of list',
				], $allPosition, [ 'class' => 'radio radioLine-2' ])
		?>
    </div>
</fieldset>

<style type="text/css">
	.ccm-ui label.control-label.radioLine-2,
	.ccm-ui label.control-label.radioLine-3 {
		display: block;
	}
	.ccm-ui div.radio.radioLine-2,
	.ccm-ui div.radio.radioLine-3 {
		display: inline-flex;
		margin-left: 10px;
	}
	.ccm-ui div.radio.radioLine-2 {
		width: 45%;
	}
	.ccm-ui div.radio.radioLine-3 {
		width: 30%;
	}
</style>
<script type="text/javascript">
    $(function() {
        $("input[name=filterByParent]").on('change', function() {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-parent-page').slideDown();
            } else {
                $('#ccm-block-related-pages-parent-page').slideUp();
            }
        }).trigger('change');
        $("input[name=redirectToResults]").on('change', function() {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-search-page').slideDown();
            } else {
                $('#ccm-block-related-pages-search-page').slideUp();
            }
        }).trigger('change');
    });
</script>
