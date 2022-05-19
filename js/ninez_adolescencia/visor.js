/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
	if (num > 1) {	//1=CARATULA
		pageRendering = true;
		current = getPageInfo(num);
		// Using promise to fetch the page
		pdfDocs[current.documentIndex].getPage(current.pageNumber).then(function(page) {
			var viewport = page.getViewport({scale: scale});

			// Prepare canvas using PDF page dimensions
			var canvas = document.getElementById('hoja' + num);
			var ctx = canvas.getContext('2d');
			canvas.height = viewport.height;
			canvas.width = viewport.width;

			// Render PDF page into canvas context
			var renderContext = {
				canvasContext: ctx,
				viewport: viewport
			};

			var renderTask = page.render(renderContext);
			// Wait for rendering to finish
			renderTask.promise.then(function() {
				pageRendering = false;
				if (pageNumPending !== null) {
					// New page rendering is pending
					renderPage(pageNumPending);
					pageNumPending = null;
				}
			});
		});
	}
	// Update page counters
	document.getElementById('page_num').textContent = pageNum;
}

/**
 * If another page rendering in progress, waits until the rendering is
 * finished. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
	if (pageRendering) {
		pageNumPending = num;
	} else {
		renderPage(num);
	}
}

/**
 * Displays first page.
 */
function onFirstPage() {
	if (typeof urls !== 'undefined' && urls.length > 0) {
		pageNum = 1;
		queueRenderPage(pageNum);
		$('.magazine').turn('page', pageNum);
	} else {
		return;
	}
}

/**
 * Displays previous page.
 */
function onPrevPage() {
	if (typeof urls !== 'undefined' && urls.length > 0) {
		if (pageNum <= 1) {
			return;
		}
		pageNum--;
		queueRenderPage(pageNum);
		$('.magazine').turn('page', pageNum);
	} else {
		return;
	}
}

/**
 * Displays next page.
 */
function onNextPage() {
	if (typeof urls !== 'undefined' && urls.length > 0) {
		if (pageNum >= totalPageCount && current.documentIndex + 1 === pdfDocs.length) {
			return;
		}

		pageNum++;
		queueRenderPage(pageNum);
		$('.magazine').turn('page', pageNum);
	} else {
		return;
	}
}

/**
 * Displays last page.
 */
function onLastPage() {
	if (typeof urls !== 'undefined' && urls.length > 0) {
		pageNum = totalPageCount;
		queueRenderPage(pageNum);
		$('.magazine').turn('page', pageNum);
	} else {
		return;
	}
}

/**
 * Displays 'num' page.
 */
function onChangePage(num) {
	if (typeof urls !== 'undefined' && urls.length > 0) {
		if (num > totalPageCount && current.documentIndex + 1 === pdfDocs.length) {
			return;
		}

		pageNum = num;
		queueRenderPage(pageNum);
	} else {
		return;
	}
}

/**
 * @returns PageNumber
 */
function getPageInfo(num) {
	for (var docIdx = 0; docIdx < pdfDocs.length; docIdx++) {
		if (num <= (pdfDocs[docIdx].numPages + 1)) { // 1=CARATULA
			return {documentIndex: docIdx, pageNumber: num - 1}; // 1=CARATULA
		}
		num -= pdfDocs[docIdx].numPages;
	}
	return false;
}

/**
 * @returns totalPageCount
 */
function getTotalPageCount() {
	var totalPageCount = 1;	// 1=CARATULA
	for (var docIdx = 0; docIdx < pdfDocs.length; docIdx++) {
		totalPageCount += pdfDocs[docIdx].numPages;
	}
	return totalPageCount;
}

var actualPage = 1;
var html = '<ul>';
html += '<li><span class="pagina">' + actualPage + '</span><a class="contenido" href="#" onclick="$(\'.magazine\').turn(\'page\', ' + actualPage + ');return false;">Carátula</a></li>';
actualPage++;
function load() {
	// Load PDFs one after another
	if (typeof urls !== 'undefined' && urls.length > 0) {
		var loadingTask = pdfjsLib.getDocument(urls[loadedCount]);
		loadingTask.promise.then(function(pdfDoc_) {
			html += '<li><span class="pagina">' + actualPage + '</span><a class="contenido" href="#" onclick="$(\'.magazine\').turn(\'page\', ' + actualPage + ');return false;">' + descripciones[loadedCount] + '</a></li>';
			actualPage += pdfDoc_.numPages;

			pdfDocs.push(pdfDoc_);
			loadedCount++;
			if (loadedCount !== urls.length) {
				return load();
			}
			totalPageCount = getTotalPageCount();
			for (let i = 2; i <= totalPageCount; i++) {	// 1=CARATULA
				$(".magazine").html($(".magazine").html() + '<div><canvas id="hoja' + i + '" style="width: 99.4%; height: 99.5%;"></canvas></div>');
			}
			document.getElementById('page_count').textContent = totalPageCount;

			// Initial/first page rendering
			renderPage(pageNum);

			// Load TurnJS
			loadTurn();

			html += '</ul>';
			$('#indice').html($('#indice').html() + html);
		});
	} else {
		totalPageCount = 1;

		document.getElementById('page_count').textContent = totalPageCount;

		// Initial/first page rendering
		renderPage(pageNum);

		// Load TurnJS
		loadTurn();

		html += '</ul>';
		$('#indice').html($('#indice').html() + html);
	}
}

function loadTurn() {
	$('#canvas').fadeIn(1000);
	var flipbook = $('.magazine');

	// Check if the CSS was already loaded
	if (flipbook.width() == 0 || flipbook.height() == 0) {
		setTimeout(loadTurn, 10);
		return;
	}

	var width = 315;
	var height = 446;
	if ($(window).height() >= 900) {
		width = 588;
		height = 832;
	} else if ($(window).height() >= 800) {
		width = 525;
		height = 746;
	} else if ($(window).height() >= 700) {
		width = 462;
		height = 654;
	} else if ($(window).height() >= 600) {
		width = 378;
		height = 534;
	}

	// Create the flipbook
	flipbook.turn({
		display: 'single',
		acceleration: !isChrome(),
		width: width,
		height: height,
		duration: 1000,
		gradients: true,
		autoCenter: true,
		elevation: 50,
		when: {
			turning: function(event, page, view) {
				if (page > totalPageCount) {
					event.preventDefault();
				} else {
					onChangePage(page);
					// Update the current URI
					Hash.go('page/' + page).update();
					// Show and hide navigation buttons
					disableControls(page);
				}
			},
			turned: function(event, page, view) {
				disableControls(page);
				$(this).turn('center');
				if (page == 1) {
					$(this).turn('peel', 'br');
				}
			}
		}
	});


	// Zoom.js
	$('.magazine-viewport').zoom({
		flipbook: $('.magazine'),
		max: function() {
			return 2;
		},
		when: {
			swipeLeft: function() {
				$(this).zoom('flipbook').turn('next');
			},
			swipeRight: function() {
				$(this).zoom('flipbook').turn('previous');
			},
			zoomIn: function() {
				$('#slider-bar').hide();
				$('.made').hide();
				$('.magazine').removeClass('animated').addClass('zoom-in');
				$('.zoom-icon').removeClass('zoom-icon-in').addClass('zoom-icon-out');
				if (!window.escTip && !$.isTouch) {
					escTip = true;
					$('<div />', {'class': 'exit-message'}).
									html('<div>Presione ESC para salir</div>').
									appendTo($('body')).
									delay(2000).
									animate({opacity: 0}, 500, function() {
										$(this).remove();
									});
				}
			},
			zoomOut: function() {
				$('#slider-bar').fadeIn();
				$('.exit-message').hide();
				$('.made').fadeIn();
				$('.zoom-icon').removeClass('zoom-icon-out').addClass('zoom-icon-in');
				setTimeout(function() {
					$('.magazine').addClass('animated').removeClass('zoom-in');
					resizeViewport();
				}, 0);
			}
		}
	});

	// Zoom event
	if ($.isTouch) {
		$('.magazine-viewport').bind('zoom.doubleTap', zoomTo);
	} else {
		$('.magazine-viewport').bind('zoom.tap', zoomTo);
	}

	// Using arrow keys to turn the page
	$(document).keydown(function(e) {
		var previous = 37, next = 39, esc = 27;
		switch (e.keyCode) {
			case previous:
				// left arrow
				$('.magazine').turn('previous');
				e.preventDefault();
				break;
			case next:
				//right arrow
				$('.magazine').turn('next');
				e.preventDefault();
				break;
			case esc:
				$('.magazine-viewport').zoom('zoomOut');
				e.preventDefault();
				break;
		}
	});

	// URIs - Format #/page/1
	Hash.on('^page\/([0-9]*)$', {
		yep: function(path, parts) {
			var page = parts[1];
			if (page !== undefined) {
				if ($('.magazine').turn('is')) {
					$('.magazine').turn('page', page);
				}
			}
		},
		nop: function(path) {
			if ($('.magazine').turn('is'))
				$('.magazine').turn('page', 1);
		}
	});

	$(window).resize(function() {
		resizeViewport();
	}).bind('orientationchange', function() {
		resizeViewport();
	});

	// Events for the next button
	$('.next-button').bind($.mouseEvents.over, function() {
		$(this).addClass('next-button-hover');
	}).bind($.mouseEvents.out, function() {
		$(this).removeClass('next-button-hover');
	}).bind($.mouseEvents.down, function() {
		$(this).addClass('next-button-down');
	}).bind($.mouseEvents.up, function() {
		$(this).removeClass('next-button-down');
	}).click(function() {
		$('.magazine').turn('next');
	});

	// Events for the previous button
	$('.previous-button').bind($.mouseEvents.over, function() {
		$(this).addClass('previous-button-hover');
	}).bind($.mouseEvents.out, function() {
		$(this).removeClass('previous-button-hover');
	}).bind($.mouseEvents.down, function() {
		$(this).addClass('previous-button-down');
	}).bind($.mouseEvents.up, function() {
		$(this).removeClass('previous-button-down');
	}).click(function() {
		$('.magazine').turn('previous');
	});

	resizeViewport();
	$('.magazine').addClass('animated');

	// Zoom icon
	$('.zoom-icon').bind('mouseover', function() {
		if ($(this).hasClass('zoom-icon-in')) {
			$(this).addClass('zoom-icon-in-hover');
		}
		if ($(this).hasClass('zoom-icon-out')) {
			$(this).addClass('zoom-icon-out-hover');
		}
	}).bind('mouseout', function() {
		if ($(this).hasClass('zoom-icon-in')) {
			$(this).removeClass('zoom-icon-in-hover');
		}

		if ($(this).hasClass('zoom-icon-out')) {
			$(this).removeClass('zoom-icon-out-hover');
		}
	}).bind('click', function() {
		if ($(this).hasClass('zoom-icon-in')) {
			$('.magazine-viewport').zoom('zoomIn');
		} else if ($(this).hasClass('zoom-icon-out')) {
			$('.magazine-viewport').zoom('zoomOut');
		}
	});
}
$('#canvas').hide();