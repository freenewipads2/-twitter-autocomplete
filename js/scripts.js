function getData(s) {
    /*
    Fetch data from input
    */
    var data = $.get("../get.php?s=" + s, function(data) {
            console.log("Loading data.");
        })
        /*
        If completed
        */
        .complete(function() {
            if (data.responseText) {
                try {
                    var obj = JSON.parse(data.responseText);
                    generateContent(obj);
                } catch (e) {
                    console.log("No valid json string generated.");
                    generateMessage("Could not fetch data");
                }
            }

        })
        .fail(function() {
            generateMessage("Could not fetch data");
            return false;
        })
}

function cleanUp() {
    //Empty data element
    $(".data-output").empty();
}

function generateMessage(s) {
    cleanUp();
    var objWrapper = document.getElementsByClassName("data-output")[0];
    objWrapper.innerHTML += "<div class='data-item'><b><center>" + s + "</center></b></div>";

}

function generateContent(obj) {
    /*
    if list is empty
    */
    if (obj.length === 0) {
        generateMessage("No tweets found.");
        return;
    }
    cleanUp();
    var objWrapper = document.getElementsByClassName("data-output")[0];
    for (i in obj) {
        objWrapper.innerHTML += "<div class='data-item'> <b>" + obj[i].user.name + "</b> : " + obj[i].text + "</div>";
    }

}

$(document).ready(function() {
    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 200; //time in ms, 5 second for example
    var $input = $('.nav-search-field');

    //on keyup, start the countdown
    $input.on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function() {
        clearTimeout(typingTimer);
    });
    //When done typing
    function doneTyping() {

        var dataInput = encodeURI($(".nav-search-field").val());
        //If input is < 1, clean up
        if (dataInput.length < 1) {
            cleanUp();
        }
        //Else fetch data
        else if (dataInput.length > 1) {
            getData(dataInput);
        }

    }
    //Focus on page load
    $("input:text:visible:first").focus();
});
