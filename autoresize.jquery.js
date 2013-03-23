/*!
 * jQuery Textarea Auto-Resize Plugin
 * http://www.beansandcurry.com/
 *
 * Copyright 2012, Beans & Curry
 * Released under the MIT License
 */
(function ($) {
  /* Auto-resize plugin */
  $.fn.autoresize = function (options) {

    var settings = $.extend({
        debug: false,
      }, options),
      styles = [
        'font-family',
        'font-size',
        'font-weight',
        'font-style',
        'letter-spacing',
        'text-transform',
        'word-spacing',
        'text-indent',
        'line-height',
        'padding-top',
        'padding-bottom'
      ];

    /* Replaces line breaks with <br /> tags for the text entered in the textarea */
    function textarea2div(text) {
      var breakTag = '<br />';
      return (text + '<br />~').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }
    
    return this.each(function () {
      var $this = $(this),
        mirror = $("<div></div>");
      /* Disables scrollbars in the textarea */
      $this.css('overflow', 'hidden');
      /* Copy the styles from the textarea to the mirror */
      $.each(styles, function (index, property) {
        mirror.css(property, $this.css(property));
      });

      mirror.css({
        'word-wrap': 'break-word',
        'position': 'absolute',
        'height': 'auto',
        'width': $this.width()
      })

      if (settings.debug === false) {
        /* Position the mirror outside of the screen */
        mirror.css({
          'top': '-999em',
          'left': '-999em'
        });
      } else {
        /* Position the mirror on the screen for debugging purposes */
        mirror.css({
          'top': '10px',
          'left': '10px'
        });
      }
      /* Copy any text that is in the textarea to the mirror */
      mirror.html(textarea2div($this.val()));
      /* Append the mirror to the body of your HTML */
      $("body").append(mirror);

      /* Make the textarea the same height as the mirror's height */
      $this.height(mirror.height());

      /* Use the textchange event to update the mirror's text and update the textarea's height */
      /* Tip: You can add "transition: height .2s" to your textarea's CSS to get a nice animation when the height changes. */
      $this.bind("textchange", function () {
        mirror.html(textarea2div($this.val()));
        $this.height(mirror.height());
      });
    });
  };

  /* Defining the 'textchange' event */
  /* Part of this code was taken from ZURB's jQuery TextChange Plugin http://www.zurb.com/playground/jquery-text-change-custom-event */
  $.event.special.textchange = {
    
    setup: function (data, namespaces) {
      $(this).data('lastValue', this.contentEditable === 'true' ? $(this).html() : $(this).val());
      $(this).bind('keyup.textchange', $.event.special.textchange.handler);
      $(this).bind('cut.textchange paste.textchange input.textchange', $.event.special.textchange.delayedHandler);
    },
    
    teardown: function (namespaces) {
      $(this).unbind('.textchange');
    },
    
    handler: function (event) {
      $.event.special.textchange.triggerIfChanged($(this));
    },
    
    delayedHandler: function (event) {
      var element = $(this);
      setTimeout(function () {
        $.event.special.textchange.triggerIfChanged(element);
      }, 25);
    },
    
    triggerIfChanged: function (element) {
      var current = element[0].contentEditable === 'true' ? element.html() : element.val();
      if (current !== element.data('lastValue')) {
        element.trigger('textchange',  [element.data('lastValue')]);
        element.data('lastValue', current);
      }
    }
  };
})(jQuery);