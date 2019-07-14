var container, preview_container, preview_loader, preview_render;
var pageList ={
	init:function(){
		this.blockForm=document.forms['ccm-block-form'];
		this.cParentIDRadios=this.blockForm.cParentID;
		for(var i=0;i<this.cParentIDRadios.length;i++){
			this.cParentIDRadios[i].onclick  = function(){ pageList.locationOtherShown(); pageList.includeAllDescendentsShown(); };
			this.cParentIDRadios[i].onchange = function(){ pageList.locationOtherShown(); pageList.includeAllDescendentsShown(); };
		}

		this.rss=document.forms['ccm-block-form'].rss || [];
		for(var i=0;i<this.rss.length;i++){
			this.rss[i].onclick  = function(){ pageList.rssInfoShown(); };
			this.rss[i].onchange = function(){ pageList.rssInfoShown(); };
		}

		this.truncateSwitch=$('#ccm-pagelist-truncateSummariesOn');
		this.truncateSwitch.click(function(){ pageList.truncationShown(this); });
		this.truncateSwitch.change(function(){ pageList.truncationShown(this); });

		$('.pagelist-form').on('change.page-list-block', 'input[name=topicFilter]', function() {
            if ($(this).val() == 'related') {
                $('div[data-row=related-topic]').slideDown();
                $('div[data-row=custom-topic]').slideUp();
            } else if ($(this).val() == 'custom') {
                $('div[data-row=related-topic]').slideUp();
                $('div[data-row=custom-topic]').slideDown();
            } else {
                $('div[data-row=related-topic]').slideUp();
                $('div[data-row=custom-topic]').slideUp();
            }
        });
		$('.pagelist-form').on('change.page-list-block', 'select[name=orderBy]', function() {
            if ($(this).val().indexOf('chrono') === 0) {
                $('.ccm-page-list-display-results').slideDown();
            } else {
                $('.ccm-page-list-display-results').slideUp();
            }
        });
        $('.pagelist-form').on('change.page-list-block', 'input[name=includeName]', function() {
            if ($(this).val() !== '0') {
                $('.ccm-page-list-show-name').slideDown();
            } else {
                $('.ccm-page-list-show-name').slideUp();
            }
        });
        $('.pagelist-form').on('change.page-list-block', 'input[name=includeDate]', function() {
            if ($(this).val() !== 'no') {
                $('.ccm-page-list-date-pos').slideDown();
            } else {
                $('.ccm-page-list-date-pos').slideUp();
            }
        });
        $('.pagelist-form').on('change.page-list-block', 'select[name=displayThumbnail]', function() {
            if ($(this).val() > 0) {
                $('.ccm-page-list-thumbnail-pos').slideDown();
            } else {
                $('.ccm-page-list-thumbnail-pos').slideUp();
            }
        });
		$('.pagelist-form').on('change.page-list-block', 'input[name=useButtonForLink]', function() {
            if (parseInt($(this).val()) >= 1) {
                $('.ccm-page-list-button-text').slideDown();
            } else {
                $('.ccm-page-list-button-text').slideUp();
            }
        });
        $('.pagelist-form').on('change.page-list-block', 'input[name=includeDescription]', function() {
            if ($(this).val() === '1') {
                $('.ccm-page-list-truncate-description').slideDown();
            } else {
                $('.ccm-page-list-truncate-description').slideUp();
            }
        });

        this.showFilterDateOptionDetails();
        $('.pagelist-form .filterDateOption').on('change', this.showFilterDateOptionDetails);

	},
	showFilterDateOptionDetails:function() {
		$('.pagelist-form .filterDateOptionDetail').hide();
		$('.pagelist-form .filterDateOption:checked').each(function(){
			$('.pagelist-form .filterDateOptionDetail[data-filterDateOption='+$(this).val()+']').show();
		});
	},
	truncationShown:function(cb){
		var truncateTxt=$('#ccm-pagelist-truncateTxt');
		var f=$('#ccm-pagelist-truncateChars');
		if(cb.checked){
			truncateTxt.removeClass('faintText');
			f.attr('disabled',false);
		}else{
			truncateTxt.addClass('faintText');
			f.attr('disabled',true);
		}
	},
	showPane:function(pane){
		$('ul#ccm-pagelist-tabs li').each(function(num, el){ $(el).removeClass('ccm-nav-active'); });
		$(document.getElementById('ccm-pagelist-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-pagelistPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-pagelistPane-'+pane).slideDown();
		if(pane === 'preview') this.loadPreview();
	},
	locationOtherShown:function(){
		for(var i=0;i<this.cParentIDRadios.length;i++){
			if( this.cParentIDRadios[i].checked && this.cParentIDRadios[i].value === 'OTHER' ){
				$('div.ccm-page-list-page-other').slideDown();
				return;
			}
		}
		$('div.ccm-page-list-page-other').slideUp();
	},
	includeAllDescendentsShown:function() {
		for (var i=0, len=this.cParentIDRadios.length; i<len; i++) {
			var cParentID = this.cParentIDRadios[i].value;
			if (this.cParentIDRadios[i].checked && (cParentID === 'OTHER' || parseInt(cParentID) > 0)) {
				$('div.ccm-page-list-all-descendents').slideDown();
				return;
			}
		}
		$('div.ccm-page-list-all-descendents').slideUp();
	},
	rssInfoShown:function(){
		for(var i=0;i<this.rss.length;i++){
			if( this.rss[i].checked && this.rss[i].value === '1' ){
				$('#ccm-pagelist-rssDetails').slideDown();
				return;
			}
		}
		$('#ccm-pagelist-rssDetails').slideUp();
	},
	loadPreview:function(){

		var query = $(this.blockForm).serializeArray();
        query.push({
            name: "current_page",
/* global CCM_CID */
            value: CCM_CID
        });
        $.get('/ccm/tds_page_list/preview', query, function(msg) {
			preview_container.find('div.render').html('<div class="cover"></div>' + msg);
            pageList.hideLoader();
        }).fail(function() {
            pageList.hideLoader();
        });
	},

    showLoader: function (element) {
        var position = element.position(),
            top = element.position().top,
            group, left;

        if (element.is('input[type=checkbox]')) {
            group = element.closest('div.checkbox');
        } else if (element.is('input[type=radio]')) {
            group = element.closest('div.radio');
        } else {
            group = element.closest('div.form-group');
        }

        left = group.position().left + group.width() + 10;

        preview_loader.css({
            left: left,
            top: top
        }).show();
    },
    hideLoader: function() {
        preview_loader.hide();
    }
};

/* global Concrete, _ */
Concrete.event.bind('pagelist.edit.open', function() {

    pageList.init();

    container = $('div.pagelist-form');
    preview_container = $('#ccm-tab-content-page-list-preview div.preview');
    preview_loader = container.find('div.loader');
    preview_render = $('div.render', preview_container);

    var handle_event = _.debounce(function(event) {
        pageList.showLoader($(event.target));
        pageList.loadPreview();
    }, 250);

    container.closest('form').change(handle_event).find('input.form-control, textarea').keyup(handle_event);
    _.defer(function() {
        pageList.loadPreview();
    });
});
