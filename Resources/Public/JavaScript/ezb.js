//this script is using for the participations form of the ezp plugin
// Only do anything if jQuery isn't defined
if (typeof jQuery == 'undefined') {

    if (typeof $ == 'function') {
        // warning, global var
        thisPageUsingOtherJSLibrary = true;
    }

    function getScript(url, success) {

        var script     = document.createElement('script');
        var domain =  window.location.host;
        script.src = domain+url;

        var head = document.getElementsByTagName('head')[0],
        done = false;

        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {

            if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {

                done = true;
                // callback function provided as param
                success();

                script.onload = script.onreadystatechange = null;
                head.removeChild(script);
            };

        };

            head.appendChild(script);
    };

    getScript('/typo3conf/ext/libconnect/Resources/Public/JavaScript/jquery-3.3.1.min.js', function() {

        if (typeof jQuery=='undefined') {
            // Super failsafe - still somehow failed...

        } else {
            // jQuery loaded! Make sure to use .noConflict just in case
            fancyCode();

            if (thisPageUsingOtherJSLibrary) {

                // Run your jQuery Code
            } else {
                // Use .noConflict(), then run your jQuery Code
            }
        }
    });

};

function setCountries(){
    //make all visible
    $("#categories option").css("display", "block");
    $("#participants option").css("display", "block");
    var selectedCountries = $("#countries").val();

    if(selectedCountries == 'all'){
        //filter categories
        $("#categories option").css("display", "block");
        setCategories();
    }else{
        //filter categories
        $("#categories option").css("display", "none");
        $("#categories ."+selectedCountries).css("display", "block");
        //set category to "all"
        $("#categories").val("all");
        //filter participants
        $("#participants option").css("display", "none");
        $("#participants ."+selectedCountries).css("display", "block");
    }

   $("#categories .all").css("display", "block");

   $("#categories").removeAttr("disabled");
}

function setCategories(){
    var selectedCategories = $("#categories").val();
    var selectedCountries = $("#countries").val();

    if(selectedCategories == 'all'){
        $("#participants ."+selectedCountries).css("display", "block");

    }else{

        $("#participants option").css("display", "none");

        if(selectedCountries == 'all'){
            $("#participants ."+selectedCategories).css("display", "block");

        }else{
            $("#participants ."+selectedCategories+"."+selectedCountries).css("display", "block");
        }

    }
    $("#categories .all").css("display", "block");
}

function changeBib(){
    var bibId = $("#participants").val();
    $("#bibinput").attr("value", bibId);
}
