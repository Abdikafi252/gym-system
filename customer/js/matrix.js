
$(document).ready(function(){

	
	
	// === Sidebar navigation === //
	
	$('.submenu > a').on('click', function(e)
	{
		e.preventDefault();
		var li = $(this).parent('li');
		var submenu = li.children('ul');
		var siblingSubmenus = li.siblings('.submenu');

		siblingSubmenus.removeClass('open')
			.children('ul')
			.stop(true, true)
			.slideUp(180);

		if(li.hasClass('open'))
		{
			li.removeClass('open');
			submenu.stop(true, true).slideUp(180);
		} else 
		{
			li.addClass('open');
			submenu.stop(true, true).slideDown(180);
		}
	});
	
	var ul = $('#sidebar > ul');
	var mobileBreakpoint = 991;
	
	$('#sidebar > a').click(function(e)
	{
		e.preventDefault();
		if($(window).width() > mobileBreakpoint)
		{
			return;
		}
		var sidebar = $('#sidebar');
		if(sidebar.hasClass('open'))
		{
			sidebar.removeClass('open');
			$('body').removeClass('mobile-nav-open');
			ul.slideUp(250);
		} else 
		{
			sidebar.addClass('open');
			$('body').addClass('mobile-nav-open');
			ul.slideDown(250);
		}
	});

	$('#sidebar > ul a').click(function()
	{
		if($(window).width() > mobileBreakpoint)
		{
			return;
		}

		var parentLi = $(this).closest('li');
		var isSubmenuToggle = parentLi.hasClass('submenu') && $(this).attr('href') === '#';
		if(isSubmenuToggle)
		{
			return;
		}

		$('#sidebar').removeClass('open');
		$('body').removeClass('mobile-nav-open');
		ul.slideUp(200);
	});

	$(document).on('click', function(e)
	{
		if($(window).width() > mobileBreakpoint)
		{
			return;
		}

		var sidebar = $('#sidebar');
		if(!sidebar.hasClass('open'))
		{
			return;
		}

		var target = $(e.target);
		var isInsideSidebar = target.closest('#sidebar').length > 0;
		var isNavButton = target.closest('#sidebar > a').length > 0;
		if(isInsideSidebar || isNavButton)
		{
			return;
		}

		sidebar.removeClass('open');
		$('body').removeClass('mobile-nav-open');
		ul.slideUp(200);
	});

	$(document).on('keydown', function(e)
	{
		if($(window).width() > mobileBreakpoint)
		{
			return;
		}

		if(e.key === 'Escape' && $('#sidebar').hasClass('open'))
		{
			$('#sidebar').removeClass('open');
			$('body').removeClass('mobile-nav-open');
			ul.slideUp(200);
		}
	});

	function syncSidebarState()
	{
		if($(window).width() <= mobileBreakpoint)
		{
			if(!$('#sidebar').hasClass('open'))
			{
				ul.css({'display':'none'});
				$('body').removeClass('mobile-nav-open');
			}
			$('#user-nav > ul').css({width:'auto','margin-left':'0'});
			$('#content-header .btn-group').css({width:'auto','margin-left':'0'});
		}
		else
		{
			$('#sidebar').removeClass('open');
			$('body').removeClass('mobile-nav-open');
			ul.css({'display':'block'});
			$('#user-nav > ul').css({width:'auto',margin:'0'});
			$('#content-header .btn-group').css({width:'auto','margin-left':'0'});
		}
	}

	$('#sidebar li.submenu.active').addClass('open').children('ul').show();
	
	// === Resize window related === //
	$(window).resize(function()
	{
		syncSidebarState();
		if($(window).width() < 479)
		{
			fix_position();
		}
	});
	
	syncSidebarState();
	if($(window).width() < 479)
	{
		fix_position();
	}
	
	// === Tooltips === //
	$('.tip').tooltip();	
	$('.tip-left').tooltip({ placement: 'left' });	
	$('.tip-right').tooltip({ placement: 'right' });	
	$('.tip-top').tooltip({ placement: 'top' });	
	$('.tip-bottom').tooltip({ placement: 'bottom' });	
	
	// === Search input typeahead === //
	$('#search input[type=text]').typeahead({
		source: ['Dashboard','Form elements','Common Elements','Validation','Wizard','Buttons','Icons','Interface elements','Support','Calendar','Gallery','Reports','Charts','Graphs','Widgets'],
		items: 4
	});
	
	// === Fixes the position of buttons group in content header and top user navigation === //
	function fix_position()
	{
		var uwidth = $('#user-nav > ul').width();
		$('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});
        
        var cwidth = $('#content-header .btn-group').width();
        $('#content-header .btn-group').css({width:cwidth,'margin-left':'-' + uwidth / 2 + 'px'});
	}
	
	// === Style switcher === //
	$('#style-switcher i').click(function()
	{
		if($(this).hasClass('open'))
		{
			$(this).parent().animate({marginRight:'-=190'});
			$(this).removeClass('open');
		} else 
		{
			$(this).parent().animate({marginRight:'+=190'});
			$(this).addClass('open');
		}
		$(this).toggleClass('icon-arrow-left');
		$(this).toggleClass('icon-arrow-right');
	});
	
	$('#style-switcher a').click(function()
	{
		var style = $(this).attr('href').replace('#','');
		$('.skin-color').attr('href','css/maruti.'+style+'.css');
		$(this).siblings('a').css({'border-color':'transparent'});
		$(this).css({'border-color':'#aaaaaa'});
	});
	
	$('.lightbox_trigger').click(function(e) {
		
		e.preventDefault();
		
		var image_href = $(this).attr("href");
		
		if ($('#lightbox').length > 0) {
			
			$('#imgbox').html('<img src="' + image_href + '" /><p><i class="icon-remove icon-white"></i></p>');
		   	
			$('#lightbox').slideDown(500);
		}
		
		else { 
			var lightbox = 
			'<div id="lightbox" style="display:none;">' +
				'<div id="imgbox"><img src="' + image_href +'" />' + 
					'<p><i class="icon-remove icon-white"></i></p>' +
				'</div>' +	
			'</div>';
				
			$('body').append(lightbox);
			$('#lightbox').slideDown(500);
		}
		
	});
	

	$('#lightbox').live('click', function() { 
		$('#lightbox').hide(200);
	});
	
});

