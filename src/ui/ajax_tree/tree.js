function collpase(id, value) {
    document.getElementById(id).style.display = "none";
    var str = 'symbol_' + id.replace(/_/g, '');
    if (document.getElementById(str)) {
        var mainid = id.replace(/_/g, '');
        var symbolhref = '<span id="symbol_' + mainid + '"><a href="javascript:expand(\'' + id + '\',' + value + ');" class="nodecls">' + UI.expand + '</a></span>';
        document.getElementById(str).innerHTML = symbolhref;
    }
}
function expand(id, value) {
    loadChild(id, value);
    document.getElementById(id).style.display = "block";
    var str = 'symbol_' + id.replace(/_/g, '');
    if (document.getElementById(str)) {
        var mainid = id.replace(/_/g, '');
        var symbolhref = '<span id="symbol_' + mainid + '"><a href="javascript:collpase(\'' + id + '\',' + value + ');" class="nodecls">' + UI.collapse + '</a></span>';
        document.getElementById(str).innerHTML = symbolhref;
    }
}
function loadChild(id, value) {
    var strParam = "services.php?method=getCat&id=" + id + "&catid=" + value;
    Ajax.Request(strParam, generateChild);
}
function generateChild() {
    Ajax.setShowMessage(1);
    Ajax.setMessage("Loaded..");
    if (Ajax.CheckReadyState(Ajax.request)) {
        var response = eval('(' + Ajax.request.responseText + ')');
        var str = '';
        var i = 0;
        if (response.data.length == 0) {
            document.getElementById(response.id).style.display = "none";
        }
        var mainid = response.id.replace(/_/g, '');
        for (i = 0; i < response.data.length; i++) {
            str += '<div id="' + mainid + '' + i + '" style="padding-left:20px;">';
            str += '<span id="symbol_' + mainid + '' + i + '"><a href="javascript:expand(\'' + response.id + '_' + i + '\',' + response.data[i].id + ');" class="nodecls">' + UI.expand + '</a></span>';
            str += '<a href="javascript:getData(' + response.data[i].id + ');" class="nodecls">' + response.data[i].name + '</a></div>';
            str += '<div id="' + response.id + '_' + i + '" style="padding-left:20px;display:none"></div>';
        }
        document.getElementById(response.id).innerHTML = str;
    }
}