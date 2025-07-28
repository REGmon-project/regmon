"use strict";

// Sticky NavBar

var stickyNavBar;

jQuery(function() 
{
stickyNavBar = {
    flagAdd: true,
	first_time: true,
	header_offset: 99,
	up_offset: 15,
	direction: 'up',
	pos: 0,
    elements: [],
    init: function (elements) {
		this.pos = window.pageYOffset;
		this.direction = this.get_direction();
        this.elements = elements;
    },
    add : function() {
        if (this.flagAdd) {
			//only when dashboard is closed and V_ANIMATE_RUN not true
			if ($('#dashboard_div').width() == '0' && !V_ANIMATE_RUN) {
				$('#nav-header').addClass('navbar-fixed-top');
				for (let i=0; i < this.elements.length; i++) {
					$('#'+this.elements[i]).addClass('fixed-theme');
				}
				this.flagAdd = false;
				this.first_time = false;
			}
        }
    },
    remove: function() {
		$('#nav-header').removeClass('navbar-fixed-top');
        for (let i=0; i < this.elements.length; i++) {
			$('#'+this.elements[i]).removeClass('fixed-theme');
        }
        this.flagAdd = true;
		this.first_time = false;
    },
	get_direction: function() {
		if (this.pos >= window.pageYOffset) {
			this.direction = 'up';
		} else {
			this.direction = 'down';
		}
		return this.direction;
	},
	get_offset_diff: function() {
		return this.pos > this.header_offset;
	},
	manager: function() {
		const offset_diff = this.pos - window.pageYOffset;
		this.get_direction();
		this.pos = window.pageYOffset;
		
		if (this.get_offset_diff() && this.first_time) {
			this.add();
		}
		else if(this.get_offset_diff() && this.direction == 'up' && offset_diff > this.up_offset) {
			this.add();
		}
		else if(this.get_offset_diff() && this.direction == 'down') {
			this.remove();
		}
		else if(!this.get_offset_diff()){
			this.remove();
		}
	}
};

//Init the object. Pass the object the array of elements that we want to change when the scroll goes down
stickyNavBar.init(  [
    "nav-header",
    "nav-header-container",
    "nav-header-logo",
    "main_container",
    "dashboard_link2"
]);

//bind to the document scroll detection
//window.onscroll = function(e) { stickyNavBar.manager(); }
$(window).on('scroll',function() {
	if (stickyNavBar.get_direction() == 'down') {
		stickyNavBar.manager();
	}
	else {
		clearTimeout($.data(this, 'scrollTimer'));

		$.data(this, 'scrollTimer', setTimeout(function() {
			stickyNavBar.manager();
		}, 100));
	}
});

//We have to do a first detectation of offset because the page could be load with scroll down set.
stickyNavBar.manager();
});