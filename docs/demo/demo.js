var api = '';
var themes = [];
var spinner =  '<img src="spinner.gif">';

function scrollTo(target) {
    var scrollTarget = $(target);
    $('html,body').animate({scrollTop: scrollTarget.offset().top});
}

function loadServices() {
    $.getJSON(api+'/?service=info&format=json', function( data ) {
        $.each( data['services'], function( key, val ) {
            $("#services").append( "<div class='servicelist'><p><a class='btn btn-primary btn-lock service main-button' id='" + val + "'>Try the " + val + " service</a></p></div>" );
        });
        $('#details').html('');
        $('#details').append( " <p>We made an AJAX call to to:</p> <code>" + api + "/info/json</code>");
        $('#details').append( " <p>and received a JSON response with info about the API.</p>");
        $('#details').append( " <p>We used the services listed in the response to create the content above.</p>");
        $('#details').append( " <blockquote class='comment'><h6>You just used the INFO service</h6>We used the API's info service to build this page. You can find out more about the info service by clicking the big <b>Try the info service</b> button above.</blockquote>");
    });
}

function markActive(id) {
    $('.main-button').removeClass('btn-info').addClass('btn-primary');
    $('#'+id).addClass('btn-info');

}

function loadApiInfo() {
    markActive('info');
    $('#content').html(spinner);
    $.getJSON(api+'/?service=info&format=json', function( data ) {
        $('#content').html('<div class="row" id="contentrow"></div>');
        $('#contentrow').append( "<div class='col-xs-12'><h2>API Information</h2></div>");

        $('#contentrow').append( "<div class='col-md-3' id='serviceblock'><h3>Services</h3><ul id='servicesul'></ul></div>");
        $.each( data['services'], function( key, val ) {
            $("#servicesul").append( "<li>" + val + "</li>" );
        });

        $('#contentrow').append( "<div class='col-md-3' id='channelblock'><h3>Channels</h3><ul id='channels'></ul></div>");
        $.each( data['channels'], function( key, val ) {
            $("#channels").append( "<li>" + val + "</li>" );
        });

        $('#contentrow').append( "<div class='col-md-3' id='themeblock'><h3>Themes</h3><ul id='themes'></ul></div>");
        $.each( data['themes'], function( key, val ) {
            $("#themes").append( "<li>" + val + "</li>" );
        });

        $('#contentrow').append( "<div class='col-md-3' id='patternblock'><h3>Patterns</h3><ul id='patterns'></ul><p>Click on a pattern name to drill deeper.</p></div>");
        $.each( data['patterns'], function( key, val ) {
            $("#patterns").append( "<li><a class='clickable pattern-info' data-pattern='" + key + "'>" + val + "</a></li>" );
        });

        $('#details').html('');
        $('#details').append( " <p>We made an AJAX call to to:</p> <code>" + api + "/info/json</code>");
        $('#details').append( " <p>We used the returned JSON to build the content above.</p><p>Here's what the response looks like:</p>");
        $('#details').append( " <pre>" + JSON.stringify(data, null, '\t') + "</pre>");
        $('#details').append( " <blockquote class='comment'><h6>Here's that JSON we used earlier</h6>The JSON response you see above is also what we used initially to build the overview of API services.</blockquote>");
        scrollTo('#content');
    });
}

function loadPatternInfo(pattern) {
    if($("#patterninfo").length == 0) {
        $('#content').append( "<div class='' id='patterninfo'></div>");
    }
    $('#patterninfo').html(spinner);
    $.getJSON(api+'/?service=info&pattern='+pattern+'&format=json', function( data ) {
        $('#patterninfo').html('');
        $('#patterninfo').append( "<div class='row'><div class='col-xs-12'><h2>" + data['info']['name'] + "</h2></div></div>");

        $('#patterninfo').append( "<div class='row' id='patternrow1'></div>");

        $('#patternrow1').append( "<div class='col-md-3' id='patterninfo-col1'></div>");
        $('#patterninfo-col1').append( "<h3>General info</h3>");
        $('#patterninfo-col1').append( "<ul id='patterninfo-col1-ul'></ul>");
        $.each( data['info'], function( key, val ) {
            $("#patterninfo-col1-ul").append( "<li><b>" + key + "</b> &raquo; " +val + "</li>" );
        });

        $('#patternrow1').append( "<div class='col-md-3' id='patterninfo-col2'></div>");
        $('#patterninfo-col2').append( "<h3>Parts</h3>");
        $('#patterninfo-col2').append( "<ul id='patterninfo-col2-ul1'></ul>");
        $.each( data['parts'], function( key, val ) {
            $("#patterninfo-col2-ul1").append( "<li><b>" + key + "</b> &raquo; " +val + "</li>" );
        });
        $('#patterninfo-col2').append( "<h3>Languages</h3>");
        $('#patterninfo-col2').append( "<ul id='patterninfo-col2-ul2'></ul>");
        $.each( data['languages'], function( key, val ) {
            $("#patterninfo-col2-ul2").append( "<li><b>" + key + "</b> &raquo; " +val + "</li>" );
        });

        $('#patternrow1').append( "<div class='col-md-3' id='patterninfo-col3'></div>");
        $('#patterninfo-col3').append( "<h3>Measurements</h3>");
        $('#patterninfo-col3').append( "<ul id='patterninfo-col3-ul'></ul>");
        $.each( data['measurements'], function( key, val ) {
            $("#patterninfo-col3-ul").append( "<li><b>" + key + "</b></li>" );
        });

        $('#patternrow1').append( "<div class='col-md-3' id='patterninfo-col4'></div>");
        $('#patterninfo-col4').append( "<h3>Sampling</h3>");
        $('#patterninfo-col4').append( "<p>These groups are defined for sampling measurements:</p>");
        $('#patterninfo-col4').append( "<ul id='patterninfo-col4-ul'></ul>");
        $.each( data['models']['groups'], function( key, val ) {
            $("#patterninfo-col4-ul").append( "<li><b>" + key + "</b></li>" );
        });
        $('#patterninfo-col4').append( "<p>Measurements and options sampling is available in the <b>sample</b> service</p>");

        $('#patterninfo').append( "<div class='row' id='patternrow2'></div>");

        $('#patternrow2').append( "<div class='col-md-9' id='patterninfo-col5'></div>");
        $('#patterninfo-col5').append( "<h3>Options</h3>");
        $('#patterninfo-col5').append( "<div id='patterninfo-col5-div'></div>");
        $.each( data['options'], function( key, val ) {
            if(val['type'] == 'measure') values = "and expects a value between <span class='label label-warning'>" + val['min'] + "</span> and <span class='label label-warning'>" + val['max'] + "</span> Its default is <span class='label label-success'>" + val['default'] + "</span";
            if(val['type'] == 'percent') values = " so it expects a value between 0 and 100. Its default is " + val['default'];
            if(val['type'] == 'chooseOne') {
                values = " so you should pick one of:</p><ul> ";
                $.each(val['options'], function( subkey, subval ) { values = values + "<li><span class='label label-warning'>" + subval + "</span></li>"; });
                values = values + "</ul><p>Its default is <span class='label label-success'>" + val['default'] + "</span>";
            }
            $("#patterninfo-col5-div").append( "<h6>" + key + "</h6><p><span class='label label-info'>" + key + "</span> is of type <span class='label label-danger'>" + val['type'] + "</span> " + values + "</p>" );
        });

        $('#patternrow2').append( "<div class='col-md-3' id='patterninfo-col6'></div>");
        if (typeof data['inMemoryOf'] !== 'undefined') {
            $('#patterninfo-col6').append( "<h3>Did you know?</h3>");
            $('#patterninfo-col6').append( "<p>This pattern was named in memory of <a href='" +  data['inMemoryOf']['link'] + "' target='_BLANK'>" +  data['inMemoryOf']['name'] + "</a>.</p>");
        }

        $('#details').html('');
        $('#details').append( " <p>We made an AJAX call to to:</p> <code>" + api + "/info/" + pattern + "/json</code>");
        $('#details').append( " <p>We used the returned JSON to build the overview above.</p><p>Here's what the response looks like:</p>");
        $('#details').append( " <pre>" + JSON.stringify(data, null, '\t') + "</pre>");
        scrollTo('#patterninfo');
    });
}

function sampleDraftPatternList(type) {
    markActive(type);
    $('#content').html(spinner);
    $.getJSON(api+'/?service=info&format=json', function( data ) {
        $.each( data['themes'], function( key, val ) {
            themes[key] = val; // Store themes so we have them available in step2
        });
        $('#content').html('<div class="row" id="contentrow"></div>');
        $('#contentrow').append( "<h2>Step 1: Pick a pattern to "+type+"</h2>");

        $.each( data['patterns'], function( key, val ) {
            $("#contentrow").append( "<div class='col-md-3'><blockquote><h6>" + val + "</h6><a class='btn btn-primary btn-block clickable uppercasefirst pattern-"+type+"' data-pattern='" + key + "'>"+type+"</a></blockquote></div>" );
        });

        $('#details').html('');
        $('#details').append( " <p>We made an AJAX call to to:</p> <code>" + api + "/info/json</code>");
        $('#details').append( " <p>We used the returned JSON to build the pattern list above.</p>");
        $('#details').append( " <p>If you're curious about what the JSON response looks like, try the INFO service.</p>");
        scrollTo('#content');
    });
}

function loadPatternForm(pattern, service) {
    $.getJSON(api+'/?service=info&pattern='+pattern+'&format=json', function( data ) {
        $('#content').html('<div class="row form-group" id="contentrow"></div>');
        $('#contentrow').append( "<h2>Step 2: Submit a form</h2>");
        $('#contentrow').append( '<form class="form" id="form"></form>');
        $('#form').append( '<div class=""><div class="col-md-6" id="col1"></div><div class="col-md-6" id="col2"></div></div>');
        $('#form').append( '<div class=""><div class="col-md-4 col-md-offset-1" id="col3"></div><div class="col-md-4 col-md-offset-2" id="col4"></div></div>');
        $('#col1').append( '<table class="table mmp-form" id="col1-table"><tr class="heading"> <td colspan="2">Measurements</td> </tr></table>');
        $('#col2').append( '<table class="table mmp-form" id="col2-table"><tr class="heading"> <td colspan="2">Options</td> </tr></table>');
        $('#col3').append( '<table class="table mmp-form" id="col3-table"><tr class="heading"> <td colspan="2">General</td> </tr></table>');
        $('#col4').append( '<table class="table mmp-form" id="col4-table"><tr class="heading"> <td colspan="2">Submit</td> </tr></table>');

        var model = data['models']['default']['model'];
        $.each( data['measurements'], function( key, val ) {
            $('#col1-table').append( formRow('input', key, data['measurements'][key], 'metric'));
        });
        $.each( data['options'], function( key, val ) {
            if(val['type'] == 'chooseOne') $('#col2-table').append( formRow('chooseOne', key, val, ''));
            else $('#col2-table').append( formRow('input', key, val, 'metric'));
        });
        if(service == 'compare') {
            $('#col3-table').append( '<tr> <td class="key"><label for="samplerGroup">Sampler group</label></td> <td> <select class="form-control" id="samplerGroup" name="samplerGroup"></select> </td> </tr>');
            $.each( data['models']['groups'], function( key, val ) {
                $('#samplerGroup').append( '<option value="'+key+'">'+key+'</option>');
            });
        }
        $('#col3-table').append( '<tr> <td class="key"><label for="theme">Theme</label></td> <td> <select class="form-control" id="theme" name="theme"></select> </td> </tr>');
        $.each( themes, function( key, val ) {
            $('#theme').append( '<option value="'+val+'">'+val+'</option>');
        });
        $('#col3-table').append( '<tr> <td class="key"><label for="language">Language</label></td> <td> <select class="form-control" id="language" name="lang"></select> </td> </tr>');
        $.each( data['languages'], function( key, val ) {
            $('#language').append( '<option value="'+key+'">'+val+'</option>');
        });
        $('#col3-table').append( '<tr> <td class="key"><label for="unitsIn">Input units</label></td> <td> <select class="form-control" id="unitsIn" name="unitsIn"></select> </td> </tr>');
        $('#unitsIn').append( '<option value="metric">Metric</option>');
        $('#unitsIn').append( '<option value="imperial">Imperial</option>');
        $('#col3-table').append( '<tr> <td class="key"><label for="unitsOut">Output units</label></td> <td> <select class="form-control" id="unitsOut" name="unitsOut"></select> </td> </tr>');
        $('#unitsOut').append( '<option value="metric">Metric</option>');
        $('#unitsOut').append( '<option value="imperial">Imperial</option>');
        $('#col4-table').append( '<tr><td colspan="2"><blockquote class="comment" id="theme-msg"><p><b>What to expect</b></p><p>The button below will open your pattern in a new window</p></blockquote><a class="btn btn-block btn-primary btn-lg gapabove uppercasefirst" id="'+service+'-submit" data-pattern="'+pattern+'" target="_BLANK">'+service+' the '+pattern+'</a></td></tr>');

    });

}

function loadDraft(pattern) {
    $('#content').html(spinner);
    loadPatternForm(pattern, 'draft');
    $('#col4-table').html('');
    $('#details').html('');
    $('#details').append( " <p>We made an AJAX call to to:</p> <code>" + api + "/info/" + pattern + "/json</code>");
    $('#details').append( " <p>We used the returned JSON to build the form above.</p>");
    $('#details').append( " <p>If you're curious about what the JSON response looks like, try the INFO service.</p>");
    scrollTo('#content');
}

function loadCompare(pattern) {
    $('#content').html(spinner);
    loadPatternForm(pattern, 'compare');
}

function loadSample(pattern) {
    $('#content').html(spinner);
    $.getJSON(api+'/?service=info&pattern='+pattern+'&format=json', function( data ) {
        $('#content').html('<div class="row form-group" id="contentrow"></div>');
        $('#contentrow').append( "<h2>Step 2: What would you like to sample?</h2>");
        $('#contentrow').append( '<div class="col-md-6" id="col1"></div><div class="col-md-6" id="col2"></div>');
        $('#col1').append( '<h3>Sample measurements</h3>');
        $('#col1').append( '<ul id="col1-list"></ul>');
        $('#col2').append( '<h3>Sample options</h3>');
        $('#col2').append( '<ul id="col2-list"></ul>');
        $.each( data['models']['groups'], function( key, val ) {

            $('#col1-list').append( '<li><a href="'+api+'/?service=sample&pattern='+pattern+'&mode=measurements&samplerGroup='+key+'" target="_BLANK">'+key+'</a> ('+val.length+' models)</li>');
        });
        $.each( data['options'], function( key, val ) {
            $('#col2-list').append( '<li><a href="'+api+'/?service=sample&pattern='+pattern+'&mode=options&option='+key+'" target="_BLANK">'+key+'</a></li>');
        });
    });
    $('#details').html('');
    $('#details').append( " <p>We made an AJAX call to to:</p> <code>" + api + "/info/" + pattern + "/json</code>");
    $('#details').append( " <p>We used the returned JSON to build the list of sample links above.</p>");
    $('#details').append( " <p>If you're curious about what the JSON response looks like, try the INFO service.</p>");
    scrollTo('#content');
}

function formRow(type, key, val, units) {
    if(type == 'input') return inputRow(key, val, units);
    if(type == 'chooseOne') return chooseOneRow(key, val);
}

function inputRow(key, val, units) {
    if(units == 'imperial') {
        unitsLong = 'inch';
        unitsShort = '"';
    } else {
        unitsLong = 'centimeter';
        unitsShort = 'cm';
    }
    if (val instanceof Object) {
        if(val['type'] == 'percent') {
            attr = ' max="100" min="0" ';
            value = val['default'];
            unitsLong = 'percent';
            unitsShort = '%';
        }
        else {
            ' max = "'+val['max']+'" min="'+val['min']+'" ';
            value = val['default']/10;
        }
    } else {
        attr = '';
        value = val/10;
    }
    return '\
        <tr>\
            <td class="key">\
                <label for="'+key+'">'+key+'</label>\
            </td>\
            <td class="value">\
                <div class="input-group">\
                    <input class="mmp-units-'+unitsLong+' mmp-units form-number form-control" id="'+key+'" name="'+key+'" value="'+value+'" type="number" '+attr+'>\
                    <span class="input-group-addon not-xs">'+unitsLong+'</span>\
                    <span class="input-group-addon xs-only">'+unitsShort+'</span>\
                </div>\
            </td>\
        </tr>';
}

function chooseOneRow(key, val) {
    var options;
    var selectThis = val['default'];
    var selected = '';
    $.each( val['options'], function( key, val ) {
        if(selectThis == key) selected = ' selected ';
        else  selected = '';
        options = options + "<option value=\"" + key + "\" " + selected + ">" + val + "</option>\n";
    });
    return '\
        <tr>\
            <td class="key">\
                <label for="'+key+'">'+key+'</label>\
            </td>\
            <td class="value">\
                <div class="input-group">\
                    <select class="form-control" id="'+key+'" name="'+key+'">\
                    ' + options + '\
                    </select>\
                </div>\
            </td>\
        </tr>';
}

function draftSubmit (pattern) {
    if($("#dev").length == 0) {
        $('#content').append( "<div id='dev'></div>");
    } else {
        $("#dev").html('');
    }
    if($('#theme').val() == 'Developer') {
        $('#draft-submit').attr('href', '#details');
        $('#draft-submit').attr('target', '');
        $('#dev').append( '\
                <ul class="nav nav-tabs" role="tablist">\
                    <li role="presentation" class="active"><a id="kinttab" href="#kint" role="tab" data-toggle="tab">Developer info</a></li>\
                    <li role="presentation"><a href="#svg" role="tab" data-toggle="tab">SVG</a></li>\
                </ul>\
                <div class="tab-content">\
                    <div role="tabpanel" class="tab-pane fade in active gapabove" id="kint">'+spinner+'</div>\
                    <div role="tabpanel" class="tab-pane fade gapabove" id="svg">'+spinner+'+</div>\
                </div>\
                ');
        $.ajax({
            type: "POST",
            url: api+'/?service=draft&pattern='+pattern,
            data: $("#form").serialize(),
            success: function(data) {
                data = jQuery.parseJSON(data);
                $('#kint').html(data.debug);
                $('#svg').html(data.svg);
            }
        });
        $('#details').html('');
        $('#details').append( " <p>You selected the developer theme, which returns a bunch of information in JSON</p>");
        $('#details').append( " <p>We posted the form above to this URL:</p>");
        $('#details').append( " <code>"+api+'/draft/'+pattern+"/</code>");
        $('#details').append( " <p>We added two tabs to this page and injected the <a href='http://raveren.github.io/kint/' target='_blank'>kint debug</a> in one, and the SVG in another. Both of those were contained in the JSON returned by the API.</p>");
        scrollTo('#dev');
    } else {
        data = $("#form").serialize();
        url = api+'/?service=draft&pattern='+pattern+'&'+data
        $('#draft-submit').attr('href', url);
        $('#draft-submit').attr('target', '_BLANK');
        $('#details').html('');
        $('#details').append( " <p>We constructed the API url from the information in your form, and opened it in a new window.</p>");
        $('#details').append( " <p>Specifically, we opened this url in a new window:</p><code>"+url+"</code>");
        $('#details').append( " <blockquote class='comment gapabove'><p><b>Yes, that's a long URL</b></p><p>We could have submitted this via a POST request to <code>"+api+'/draft/'+pattern+"/</code> but this is more verbose and makes is easier to see what's going on.</p></blockquote>");
        scrollTo('#details');
    }
}

function compareSubmit (pattern) {
    if($("#dev").length == 0) {
        $('#content').append( "<div id='dev'></div>");
    } else {
        $("#dev").html('');
    }
    data = $("#form").serialize();
    url = api+'/?service=compare&pattern='+pattern+'&mode=measurements&'+data
        console.log(url);
    $('#compare-submit').attr('href', url);
    $('#compare-submit').attr('target', '_BLANK');
    $('#details').html('');
    $('#details').append( " <p>We constructed the API url from the information in your form, and opened it in a new window.</p>");
    $('#details').append( " <p>Specifically, we opened this url in a new window:</p><code>"+url+"</code>");
    $('#details').append( " <blockquote class='comment gapabove'><p><b>Yes, that's a long URL</b></p><p>We could have submitted this via a POST request to <code>"+api+'/compare/'+pattern+"/</code> but this is more verbose and makes is easier to see what's going on.</p></blockquote>");
    scrollTo('#details');
}
