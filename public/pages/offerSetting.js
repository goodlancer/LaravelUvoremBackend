function checkPublished(id) {
    var state = 0;
    if ($("#state" + id).is(':checked')) {
        state = 1;
    }
    var url = "user/offer_publish";
    var data = {
        id: id,
        state: state
    }
    $.post(url, data, function (data, status) {
        console.log(data);
    });
}