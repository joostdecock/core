(function ($) {
  $(document).ready(function () {

    // identify the option pack image selects
    var fitpack = 'input.fit-option-pack-radio';
    var stylepack = 'input.style-option-pack-radio';

    // Set default options on load
    mmpSetFitOptions($(fitpack+':checked'));
    mmpSetStyleOptions($(stylepack+':checked'));
    
    // Updated options on change
    $(fitpack).change(function(){ mmpSetFitOptions($(this)); });
    $(stylepack).change(function(){ mmpSetStyleOptions($(this)); });

    // Hide detailed options on load
    $('.set-by-fit-option-pack').hide();
    $('.set-by-style-option-pack').hide();
    
    // Show/Hide buttons
    $('#toggle-fit-options').click(function(){ $('.set-by-fit-option-pack').toggle(); });
    $('#toggle-style-options').click(function(){ $('.set-by-style-option-pack').toggle(); });

    /* This handles the fit option pack
     * Takes the object of the select as input */
    function mmpSetFitOptions(input) {
      // Hightlight selected choice in image select
      $('img.selected-fit-pack').removeClass('selected-fit-pack');
      var label = $("label[for='"+input.attr('id')+"'] > img");
      label.addClass('selected-fit-pack');

      // These are the options we need to set
      this.fit_fields = [
        'chest-ease',
        'natural-waist-ease',
        'trouser-waist-ease',
        'cuff-ease',
        'biceps-ease'
      ];

      // Option pack presets
      this.skinny_fit =  [10, 13, 10,   3,  4];
      this.slim_fit =    [13, 17, 12,   3,  6];
      this.regular_fit = [16, 21, 15,   3,  8];
      this.casual_fit =  [20, 26, 18,   4, 10];
      this.loose_fit =   [24, 30, 22,   4, 12];
      
      // Get to work
      var index;
      var a = this['fit_fields'];
      var value = getLabelFromValue(input.val())
      var v = this[value+'_fit'];
      console.log('==================================================');
      console.log('Fit option pack '+value);
      console.log('==================================================');
      for (index = 0; index < a.length; ++index) {
        $('#edit-configure-submitted-'+a[index]).val(v[index]);
        var thediv = $('#edit-configure-submitted-'+a[index]).parent();
        /* If this input has an input-group div, select the grandparent */
        if($(thediv).hasClass('input-group')) var thediv = $(thediv).parent();
        thediv.addClass('set-by-option-pack set-by-fit-option-pack');
        thediv.attr('data-packname','fit option pack');
        console.log('Setting '+a[index]+ ' to ' + v[index]);
      };
    }

    /* This handles the fit option pack
     * Takes the object of the select as input */
    function mmpSetStyleOptions(input) {
      // Hightlight selected choice in image select
      $('img.selected-style-pack').removeClass('selected-style-pack');
      var label = $("label[for='"+input.attr('id')+"'] > img");
      label.addClass('selected-style-pack');

      // These are the options we need to set
      this.style_fields = [
        'split-yoke',
        'button-placket-type',
        'button-placket-style',
        'buttonhole-placket-style',
        'buttonhole-placket-type',
        'extra-top-button',
        'cuff-style',
        'hem-style',
        'number-of-buttons',
        'barrelcuff-buttons',
        'barrelcuff-narrow-button',
        'button-free-length',
        'button-placket-width',
        'buttonhole-placket-width',
        'buttonhole-placket-fold-width',
        'back-neck-cutout',
        'collar-ease',
        'collar-stand-height',
        'collar-stand-bend',
        'collar-stand-curve',
        'collar-gap',
        'collar-bend',
        'collar-flare',
        'collar-angle',
        'collar-roll',
        'cuff-drape',
        'cuff-length',
        'hem-curve',
        'hip-flare',
        'length-bonus',
        'sleevecap-ease',
        'sleeve-placket-length',
        'sleeve-placket-width'
      ];

      // Option pack presets
      this.office_style_fields =     [0, 2, 1, 1, 1, 1, 3, 3, 7, 1, 1,  1, 2, 3.5, 0.635, 2 ,  3, 3.5, 0.5, 5, 2.5, 1.5,   1,  80, 0.7, 6, 6.5, 8, 2, 15, 0.4,   18, 2.5];
      this.formal_style_fields =     [0, 1, 2, 2, 1, 1, 6, 1, 7, 1, 0, -2, 2,   3, 0.635, 2 ,2.5,   4, 0.5, 5,   3, 1.5,   1, 110, 0.9, 5,   8, 0, 1, 15, 0.4,   17, 2.5];
      this.casual_style_fields =     [0, 1, 2, 2, 2, 0, 1, 2, 6, 1, 1,  2, 2,   3, 0.635, 2 ,3.5,   3,   0, 0, 2.5,   1, 0.5,  90, 0.7, 5,   7, 6, 0, 10, 0.3, 16.5, 2.5];
      this.party_style_fields =      [1, 2, 1, 1, 1, 0, 2, 1, 8, 2, 0,  1, 2, 3.5, 0.635, 2 ,3.5,   3, 0.5, 5,   2, 1.5,   0,  90, 0.5, 4,   5, 0, 0,  5, 0.3, 18.5,   2];
      this.lumberjack_style_fields = [0, 1, 2, 2, 1, 0, 3, 1, 6, 1, 1,  2, 3,   3, 0.635, 2 ,  4, 3.5,   0, 0, 2.5,   1,   1,  80, 0.7, 5,   6, 0, 0,  8, 0.5,   20, 2.5];
      
      // Get to work
      var index;
      var a = this['style_fields'];
      var value = getLabelFromValue(input.val())
      var v = this[value+'_style_fields'];
      console.log('==================================================');
      console.log('Style option pack '+value);
      console.log('==================================================');
      for (index = 0; index < a.length; ++index) {
        $('#edit-configure-submitted-'+a[index]).val(v[index]);
        var thediv = $('#edit-configure-submitted-'+a[index]).parent();
        /* If this input has an input-group div, select the grandparent */
        if($(thediv).hasClass('input-group')) var thediv = $(thediv).parent();
        thediv.addClass('set-by-option-pack set-by-style-option-pack');
        thediv.attr('data-packname','style option pack');
        console.log('Setting '+a[index]+ '  to ' + v[index]);
      };
    }

    // Helper since Drupal image select module sets value to fid, rather than something useful
    function getLabelFromValue(value) {
      return $('p.webform-select-image-legend-'+value).html().toLowerCase();
    }

  });
}(jQuery));



