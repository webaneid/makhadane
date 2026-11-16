/**
 * Makhad Ane — Main Front-End Script (Single Consolidated File)
 *
 * Hanya memuat logika yang benar-benar dipakai oleh template:
 * 1. Mobile Menu (#mobile-menu-active via MeanMenu yang dirampingkan)
 * 2. Google Maps (.acf-map pada halaman Kontak)
 * 3. Owl Carousel (.home-big-slider, [id="home-sliding"], #post-block-slider, .testimoni-slider)
 * 4. FAQ Accordion (.accordion button pada section & archive FAQ)
 * 5. Magnific Popup (.gallery-popup pada konten artikel/halaman tunggal)
 * 6. Sticky Sidebar (.sticky-top pada layout berita desktop)
 * 7. Floating WhatsApp Chat (.floating-chat & pengiriman pesan round-robin)
 * 8. Post Views AJAX Counter (postViewsCache pada artikel single)
 * 9. Linktree Click Tracking AJAX (aneLinktreeTracking pada halaman Linktree)
 */
(function ($) {
	'use strict';

	/* 1. MeanMenu (untuk #mobile-menu-active) */
	$.fn.meanmenu = function (options) {
		var defaults = {
			meanMenuTarget: $(this),
			meanMenuContainer: 'body',
			meanMenuClose: 'X',
			meanMenuCloseSize: '18px',
			meanMenuOpen: '<span></span><span></span><span></span>',
			meanRevealPosition: '',
			meanRevealPositionDistance: '0',
			meanRevealColour: '',
			meanScreenWidth: '991',
			meanNavPush: '',
			meanShowChildren: true,
			meanExpandableChildren: true,
			meanExpand: '+',
			meanContract: '-',
			meanDisplay: 'block'
		};
		options = $.extend(defaults, options);

		var currentWidth = window.innerWidth || document.documentElement.clientWidth;

		return this.each(function () {
			var meanMenu = options.meanMenuTarget;
			var meanContainer = options.meanMenuContainer;
			var meanScreenWidth = parseInt(options.meanScreenWidth, 10);
			var meanRevealClass = '.meanmenu-reveal';
			var menuOn = false;
			var meanMenuExist = false;
			var $navreveal = '';

			var meanRevealPos = '';
			if (options.meanRevealPosition === 'right') {
				meanRevealPos = 'right:' + options.meanRevealPositionDistance + ';left:auto;';
			} else if (options.meanRevealPosition === 'left') {
				meanRevealPos = 'left:' + options.meanRevealPositionDistance + ';right:auto;';
			}

			var meanInner = function () {
				if ($($navreveal).is('.meanmenu-reveal.meanclose')) {
					$navreveal.html(options.meanMenuClose);
				} else {
					$navreveal.html(options.meanMenuOpen);
				}
			};

			var meanOriginal = function () {
				$('.mean-bar,.mean-push').remove();
				$(meanContainer).removeClass('mean-container');
				$(meanMenu).css('display', options.meanDisplay);
				menuOn = false;
				meanMenuExist = false;
			};

			var showMeanMenu = function () {
				var meanStyles = 'background:' + options.meanRevealColour + ';color:' + options.meanRevealColour + ';' + meanRevealPos;
				if (currentWidth <= meanScreenWidth) {
					meanMenuExist = true;
					$(meanContainer).addClass('mean-container');
					$('.mean-container').prepend('<div class="mean-bar"><a href="#nav" class="meanmenu-reveal" aria-label="Menu Navigasi" style="' + meanStyles + '">Show Navigation</a><nav class="mean-nav"></nav></div>');
					$('.mean-nav').html($(meanMenu).html());

					$(meanMenu).before('<div class="mean-push" />');
					$('.mean-push').css('margin-top', options.meanNavPush);

					$(meanMenu).hide();
					$(meanRevealClass).show().html(options.meanMenuOpen).removeClass('meanclose');
					$navreveal = $(meanRevealClass);
					$('.mean-nav ul').hide();

					if (options.meanShowChildren) {
						if (options.meanExpandableChildren) {
							$('.mean-nav ul ul').each(function () {
								if ($(this).children().length) {
									$(this, 'li:first').parent().append('<a class="mean-expand" href="#" aria-label="Submenu" style="font-size: ' + options.meanMenuCloseSize + '">' + options.meanExpand + '</a>');
								}
							});
							$('.mean-expand').on('click', function (e) {
								e.preventDefault();
								if ($(this).hasClass('mean-clicked')) {
									$(this).text(options.meanExpand);
									$(this).prev('ul').slideUp(240);
								} else {
									$(this).text(options.meanContract);
									$(this).prev('ul').slideDown(240);
								}
								$(this).toggleClass('mean-clicked');
							});
						} else {
							$('.mean-nav ul ul').show();
						}
					} else {
						$('.mean-nav ul ul').hide();
					}

					$('.mean-nav ul li').last().addClass('mean-last');
					$navreveal.on('click', function (e) {
						e.preventDefault();
						if (!menuOn) {
							$navreveal.css({
								'text-align': 'center',
								'text-indent': '0',
								'font-size': options.meanMenuCloseSize
							});
							$('.mean-nav ul:first').slideDown(260);
							menuOn = true;
						} else {
							$('.mean-nav ul:first').slideUp(220);
							menuOn = false;
						}
						$navreveal.toggleClass('meanclose');
						meanInner();
					});
				} else {
					meanOriginal();
				}
			};

			$(window).on('resize', function () {
				currentWidth = window.innerWidth || document.documentElement.clientWidth;
				if (currentWidth <= meanScreenWidth) {
					if (!meanMenuExist) {
						showMeanMenu();
					}
				} else {
					meanOriginal();
				}
			});

			showMeanMenu();
		});
	};

	/* 2. Google Maps Helper (.acf-map) */
	function renderAcfMap($el) {
		if (typeof google === 'undefined' || !google.maps) {
			return;
		}
		var $markers = $el.find('.marker');
		var styles = [
			{ elementType: 'geometry', stylers: [{ color: '#f5f5f5' }] },
			{ elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
			{ elementType: 'labels.text.fill', stylers: [{ color: '#616161' }] },
			{ elementType: 'labels.text.stroke', stylers: [{ color: '#f5f5f5' }] },
			{ featureType: 'administrative.land_parcel', elementType: 'labels.text.fill', stylers: [{ color: '#bdbdbd' }] },
			{ featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#eeeeee' }] },
			{ featureType: 'poi', elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
			{ featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#e5e5e5' }] },
			{ featureType: 'poi.park', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] },
			{ featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
			{ featureType: 'road.arterial', elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
			{ featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#dadada' }] },
			{ featureType: 'road.highway', elementType: 'labels.text.fill', stylers: [{ color: '#616161' }] },
			{ featureType: 'road.local', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] },
			{ featureType: 'transit.line', elementType: 'geometry', stylers: [{ color: '#e5e5e5' }] },
			{ featureType: 'transit.station', elementType: 'geometry', stylers: [{ color: '#eeeeee' }] },
			{ featureType: 'water', elementType: 'geometry', stylers: [{ color: '#c9c9c9' }] },
			{ featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] }
		];

		var map = new google.maps.Map($el[0], {
			zoom: 16,
			center: new google.maps.LatLng(0, 0),
			mapTypeControl: false,
			panControl: false,
			scrollwheel: false,
			draggable: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: { mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style'] }
		});

		map.mapTypes.set('map_style', new google.maps.StyledMapType(styles, { name: 'Styled Map' }));
		map.setMapTypeId('map_style');
		map.markers = [];

		$markers.each(function () {
			var $marker = $(this);
			var latlng = new google.maps.LatLng($marker.attr('data-lat'), $marker.attr('data-lng'));
			var marker = new google.maps.Marker({ position: latlng, map: map });
			map.markers.push(marker);

			if ($marker.html()) {
				var infowindow = new google.maps.InfoWindow({ content: $marker.html() });
				google.maps.event.addListener(marker, 'click', function () {
					infowindow.open(map, marker);
				});
			}
		});

		var bounds = new google.maps.LatLngBounds();
		$.each(map.markers, function (i, marker) {
			bounds.extend(new google.maps.LatLng(marker.position.lat(), marker.position.lng()));
		});

		if (map.markers.length === 1) {
			map.setCenter(bounds.getCenter());
			map.setZoom(16);
		} else if (map.markers.length > 1) {
			map.fitBounds(bounds);
		}
	}

	/* 3. DOM Ready Initialization */
	$(function () {
		/* Owl Carousel */
		if ($.fn.owlCarousel) {
			var applyOwlAriaLabels = function ($scope) {
				$scope.find('.owl-prev').attr('aria-label', 'Slide sebelumnya');
				$scope.find('.owl-next').attr('aria-label', 'Slide berikutnya');
				$scope.find('.owl-dot').each(function (idx) {
					$(this).attr('aria-label', 'Ke slide ' + (idx + 1));
				});
			};

			var $bigSlider = $('.home-big-slider');
			if ($bigSlider.length) {
				$bigSlider
					.on('initialized.owl.carousel refreshed.owl.carousel', function (e) {
						applyOwlAriaLabels($(e.currentTarget));
					})
					.owlCarousel({
						loop: true,
						nav: false,
						autoplay: true,
						autoplayTimeout: 7000,
						animateOut: 'fadeOut',
						animateIn: 'fadeIn',
						items: 1
					});
				applyOwlAriaLabels($bigSlider);
			}

			var $homeSliding = $('[id="home-sliding"], #post-block-slider');
			if ($homeSliding.length) {
				$homeSliding
					.on('initialized.owl.carousel refreshed.owl.carousel', function (e) {
						applyOwlAriaLabels($(e.currentTarget));
					})
					.owlCarousel({
						nav: false,
						items: 4,
						autoplay: true,
						margin: 15,
						dots: true,
						autoplayHoverPause: true,
						loop: true,
						responsive: {
							0: { items: 1.2, margin: 16 },
							768: { items: 3, margin: 15 },
							1200: { items: 4 }
						}
					});
				applyOwlAriaLabels($homeSliding);
			}

			var $testimoniSlider = $('.testimoni-slider');
			if ($testimoniSlider.length) {
				$testimoniSlider
					.on('initialized.owl.carousel refreshed.owl.carousel', function (e) {
						applyOwlAriaLabels($(e.currentTarget));
					})
					.owlCarousel({
						items: 3,
						dots: false,
						loop: true,
						nav: true,
						margin: 15,
						navText: [
							'<i class="ane-icon" aria-hidden="true"><svg class="ane-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false"><path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg></i>',
							'<i class="ane-icon" aria-hidden="true"><svg class="ane-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false"><path d="M8.59 16.59 10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg></i>'
						],
						responsive: {
							0: { items: 1.2, margin: 16 },
							768: { items: 2 },
							1200: { items: 3 }
						}
					});
				applyOwlAriaLabels($testimoniSlider);
			}
		}

		/* FAQ Accordion (.accordion button) */
		if ($('.accordion button').length) {
			$('.accordion button').on('click', function () {
				var expanded = $(this).attr('aria-expanded') === 'false';
				$('.accordion button').attr('aria-expanded', 'false');
				if (expanded) {
					$(this).attr('aria-expanded', 'true');
				}
			});
		}

		/* Magnific Popup (.gallery-popup) */
		if ($.fn.magnificPopup && $('.gallery-popup').length) {
			$('.gallery-popup').magnificPopup({
				type: 'image',
				gallery: { enabled: true },
				mainClass: 'mfp-with-zoom',
				zoom: { enabled: true, duration: 300, easing: 'ease-in-out' },
				image: {
					markup:
						'<div class="mfp-figure">' +
						'<div class="mfp-close"></div>' +
						'<div class="mfp-img"></div>' +
						'<div class="mfp-bottom-bar">' +
						'<div class="mfp-title"></div>' +
						'<div class="mfp-description"></div>' +
						'<div class="mfp-counter"></div>' +
						'</div>' +
						'</div>',
					titleSrc: function (item) {
						var alt = item.el.find('img').attr('alt') || '';
						return $('<strong/>').text(alt).prop('outerHTML');
					}
				}
			});
		}

		/* Mobile Menu (#mobile-menu-active) */
		if ($('#mobile-menu-active').length) {
			$('#mobile-menu-active').meanmenu({
				meanScreenWidth: '991',
				meanMenuContainer: '.mobile-menu-area .mobile-menu',
				meanMenuOpen: '<span></span><span></span><span></span>'
			});
		}

		/* Sticky Sidebar (.sticky-top) */
		var $sidebar = $('.sticky-top');
		var $footer = $('footer');
		if ($sidebar.length && $footer.length) {
			var offsetTop = $sidebar.offset().top;
			var spacer = $('<div>').css({
				width: $sidebar.outerWidth(),
				height: $sidebar.outerHeight(),
				display: 'none'
			});

			$sidebar.before(spacer);

			var isScrollTicking = false;
			var updateStickySidebar = function () {
				isScrollTicking = false;
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				var sidebarHeight = $sidebar.outerHeight();
				var footerTop = $footer.offset().top;

				if (scrollTop > offsetTop && scrollTop + sidebarHeight < footerTop) {
					if (!$sidebar.hasClass('is-sticky')) {
						$sidebar.addClass('is-sticky').css({
							position: 'fixed',
							top: '0',
							width: spacer.width(),
							zIndex: 1000
						});
						spacer.show();
					}
				} else if (scrollTop + sidebarHeight >= footerTop) {
					$sidebar.removeClass('is-sticky').css({
						position: 'absolute',
						top: footerTop - sidebarHeight - offsetTop + 'px',
						width: spacer.width()
					});
				} else {
					$sidebar.removeClass('is-sticky').css({
						position: '',
						top: '',
						width: ''
					});
					spacer.hide();
				}
			};

			window.addEventListener(
				'scroll',
				function () {
					if (!isScrollTicking) {
						isScrollTicking = true;
						window.requestAnimationFrame(updateStickySidebar);
					}
				},
				{ passive: true }
			);
		}

		/* Floating WhatsApp Chat (.floating-chat) */
		var $floatingChat = $('.floating-chat');
		if ($floatingChat.length) {
			var $chatBox = $floatingChat.find('.chat');
			var $closeButton = $floatingChat.find('.ane-close, .ane-tutup');

			setTimeout(function () {
				$floatingChat.addClass('enter');
			}, 1000);

			$floatingChat.on('click', function () {
				$floatingChat.addClass('expand');
				$chatBox.addClass('enter');
			});

			$closeButton.on('click', function (event) {
				event.stopPropagation();
				$floatingChat.removeClass('expand');
				$chatBox.removeClass('enter');
			});

			var sendWhatsApp = function (event) {
				if (event) {
					event.preventDefault();
					event.stopPropagation();
				}
				var $input = $('#waMessage');
				var message = $input.length ? $.trim($input.val()) : '';
				if (message === '') {
					window.alert('Silakan ketik pesan terlebih dahulu.');
					return;
				}

				var numbers = [];
				var rawNumbers = $floatingChat.attr('data-wa-numbers');
				if (rawNumbers) {
					try {
						var parsed = JSON.parse(rawNumbers);
						if ($.isArray(parsed)) {
							numbers = parsed;
						}
					} catch (err) {
						numbers = [];
					}
				}

				if (!numbers.length) {
					window.alert($floatingChat.attr('data-wa-empty-msg') || 'WhatsApp number not available.');
					return;
				}

				var lastIndex = 0;
				try {
					var storedIndex = window.localStorage.getItem('waLastIndex');
					lastIndex = storedIndex ? parseInt(storedIndex, 10) : 0;
					if (isNaN(lastIndex) || lastIndex < 0) {
						lastIndex = 0;
					}
				} catch (err) {
					lastIndex = 0;
				}

				var phone = numbers[lastIndex % numbers.length];
				var nextIndex = (lastIndex + 1) % numbers.length;
				try {
					window.localStorage.setItem('waLastIndex', String(nextIndex));
				} catch (err) {
					// Abaikan jika storage penuh / private mode.
				}

				var url = 'https://wa.me/' + encodeURIComponent(phone) + '?text=' + encodeURIComponent(message);
				window.open(url, '_blank', 'noopener,noreferrer');
			};

			$floatingChat.find('.wa-button').on('click', sendWhatsApp);
			$floatingChat.find('#waMessage').on('keydown', function (e) {
				if (e.key === 'Enter') {
					sendWhatsApp(e);
				}
			});
		}

		/* Google Maps (.acf-map) */
		if ($('.acf-map').length) {
			$('.acf-map').each(function () {
				renderAcfMap($(this));
			});
		}

		/* Post Views Cache AJAX (pada artikel single) */
		if (typeof postViewsCache !== 'undefined' && postViewsCache.admin_ajax_url && postViewsCache.post_id) {
			var viewedKey = 'viewed_' + postViewsCache.post_id;
			var alreadyViewed = false;
			try {
				alreadyViewed = window.sessionStorage.getItem(viewedKey) !== null;
			} catch (err) {
				alreadyViewed = false;
			}

			if (!alreadyViewed) {
				$.post(
					postViewsCache.admin_ajax_url,
					{
						action: 'postviews',
						postviews_id: postViewsCache.post_id
					},
					function (response) {
						if (response && response.success) {
							try {
								window.sessionStorage.setItem(viewedKey, '1');
							} catch (err) {
								// Abaikan jika sessionStorage tidak tersedia.
							}
						}
					}
				);
			}
		}

		/* Linktree Click Tracking AJAX (pada template page-linktree.php) */
		if (typeof aneLinktreeTracking !== 'undefined' && aneLinktreeTracking.ajaxUrl) {
			var trackLinktreeClick = function (linkType, linkLabel, linkUrl) {
				$.ajax({
					url: aneLinktreeTracking.ajaxUrl,
					type: 'POST',
					data: {
						action: 'ane_track_linktree_click',
						nonce: aneLinktreeTracking.nonce,
						link_type: linkType,
						link_label: linkLabel,
						link_url: linkUrl
					}
				});
			};

			$('.entry-content .ane-links')
				.parent('a')
				.on('click', function () {
					var $link = $(this);
					var $box = $link.find('.ane-links');
					var linkType = 'custom';
					var linkLabel = $.trim($link.find('.ane-label').text());
					var linkUrl = $link.attr('href');

					if ($box.hasClass('whatsapp')) {
						linkType = 'whatsapp';
					} else if ($box.hasClass('website')) {
						linkType = 'website';
					} else if ($box.hasClass('tiktok')) {
						linkType = 'tiktok';
					} else if ($box.hasClass('telegram')) {
						linkType = 'telegram';
					}

					trackLinktreeClick(linkType, linkLabel, linkUrl);
				});

			$('.entry-footer .ane-sosmed a').on('click', function () {
				var $link = $(this);
				var $li = $link.find('li');
				var linkType = 'social';
				var linkLabel = $.trim($li.find('span').text());
				var linkUrl = $link.attr('href');
				var platforms = ['whatsapp', 'facebook', 'twitter', 'youtube', 'instagram', 'tiktok', 'telegram', 'threads', 'linkedin'];

				for (var i = 0; i < platforms.length; i++) {
					if ($li.hasClass(platforms[i])) {
						linkType = platforms[i];
						break;
					}
				}

				trackLinktreeClick(linkType, linkLabel, linkUrl);
			});
		}

		/* About Us Gallery Scroll Drift & Arrow Controls (.about-us) */
		var aboutSection = document.querySelector('.about-us');
		if (aboutSection) {
			var aboutViewport = aboutSection.querySelector('.about-us-gallery-viewport');
			var aboutTrack = aboutSection.querySelector('.about-us-gallery-track');
			var aboutBar = aboutSection.querySelector('.about-us-progress-bar');
			var prevBtn = aboutSection.querySelector('.about-us-nav-btn.is-prev');
			var nextBtn = aboutSection.querySelector('.about-us-nav-btn.is-next');

			if (aboutViewport && aboutTrack) {
				var manualOffset = 0;
				var scrollTicking = false;
				var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

				var getMaxShift = function () {
					return Math.max(140, aboutTrack.scrollWidth - aboutViewport.clientWidth);
				};

				var renderGallery = function () {
					scrollTicking = false;
					var maxShift = getMaxShift();
					var scrollProgress = 0;

					if (!reducedMotion) {
						var rect = aboutSection.getBoundingClientRect();
						var vh = window.innerHeight || document.documentElement.clientHeight;
						var total = vh + rect.height;
						if (total > 0) {
							scrollProgress = Math.min(1, Math.max(0, (vh - rect.top) / total));
						}
					}

					var baseShift = scrollProgress * maxShift * 0.65;
					var totalShift = Math.min(maxShift, Math.max(0, baseShift + manualOffset));
					aboutTrack.style.transform = 'translate3d(' + (-totalShift.toFixed(1)) + 'px, 0, 0)';

					if (aboutBar) {
						var pct = Math.min(100, Math.max(20, 20 + (totalShift / maxShift) * 80));
						aboutBar.style.width = pct.toFixed(1) + '%';
					}
				};

				window.addEventListener(
					'scroll',
					function () {
						if (!scrollTicking) {
							scrollTicking = true;
							window.requestAnimationFrame(renderGallery);
						}
					},
					{ passive: true }
				);

				window.addEventListener('resize', renderGallery, { passive: true });

				if (prevBtn) {
					prevBtn.addEventListener('click', function () {
						manualOffset = Math.max(-getMaxShift() * 0.5, manualOffset - 260);
						renderGallery();
					});
				}
				if (nextBtn) {
					nextBtn.addEventListener('click', function () {
						manualOffset = Math.min(getMaxShift() * 0.8, manualOffset + 260);
						renderGallery();
					});
				}

				renderGallery();
			}
		}
	});
})(jQuery);
