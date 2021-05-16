var countryListAllIsoData;
var allArticles = '';
var allImages = '';
var offerType = 3;
function getData() {
    // var id = $("#userId").val();
    // var url = "./getAllOffers";
    // console.log(offerType)
    // var data = {
    //     id: id,
    //     offerType: offerType
    // }
    // $.post(url, data, function (data, status) {
    //     allImages = data.images;
    //     allArticles = data.articles
    //     //drawTable(allArticles, allImages);
    // })
}

function selectOffer() {
    offerType = $("#selectOffer").val();
    getData();
}
                    
function checkPublished(id) {
    var state = 0;
    if ($("#state" + id).is(':checked')) {
        state = 1;
    }
    var url = "../offer_publish";
    var data = {
        id: id,
        state: state
    }
    $.post(url, data, function (data, status) {
        console.log(data);
    });
}

function userActivate(id) {
    var state = 0;
    if ($("#activate").is(':checked')) {
        state = 1;
    }
    var url = "./userActivate";
    var data = {
        id: id,
        state: state
    }
    console.log(state)
    $.post(url, data, function (data, status) {
        console.log('success');
    });
}

function countrySetting(state, id){
    var url = "user/country_state";
    var data = {
        id: id,
        state: state
    }
    $.post(url, data, function (data, status) {
        console.log(data);
    })
}

$(document).ready(function () {
    
});
