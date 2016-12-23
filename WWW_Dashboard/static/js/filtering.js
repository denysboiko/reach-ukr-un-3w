var filters = {};

for (var chart in Charts) {
    filters[chart] = [];
}

var elt = $('#filters');

elt.tagsinput({

    itemValue: 'value',
    itemText: 'text',
    itemFilter: 'filter',
    itemFilterID: 'filter',
    itemOrder: 'order',
    tagClass: function(item) {
        return item.order == 1 ? 'first-tag' + item.filterID : null;
    }
});


var updateFilters = function(filter) {

    $.extend(filters, filter);
    elt.tagsinput('removeAll');

    var fid = 0;

    for(var name in filters) {

        var start = elt.tagsinput('items').length;

        fid = fid + 1;

        if(filters[name] && filters[name].length > 0) {

            var delimeter = '<span class="tag label disabled-tag">' + name + ': </span>';

            for (i = 0; i < filters[name].length; i++) {
                elt.tagsinput('add',
                    {
                        "value": start + i + 1 ,
                        "order" : i + 1,
                        "text": filters[name][i],
                        "filter": name,
                        "filterID": fid,
                        "continent": "Europe"
                    }
                );

            }
            $(delimeter).insertBefore('.first-tag'+fid);

        }
    }
};


    //var Charts = {
    //
    //    'Data As Of' : dataasofChart,
    //    'Activity': activity_chart,
    //    'Agency': agency_chart,
    //    'Area': area_chart,
    //    'Cluster': cluster_chart,
    //    'Map': mapChart,
    //    'Oblast': oblast_chart,
    //    'Partner': partner_chart,
    //    'Start': startChart,
    //    'Status': status_chart
    //};


elt.on('itemRemoved', function(event) {

    var filterRemoved = event.item.filter;
    var textRemoved = event.item.text;
    var newfilters = [];

    j = 0;
    for (i = 0; i < filters[filterRemoved].length; i++) {
        j = j +1;
        if (filters[filterRemoved][i] != textRemoved) {
            newfilters.push(filters[filterRemoved][i]);
        }
    }

    filters[filterRemoved] = newfilters;

    if (filterRemoved == t('Start')) {
      Charts[filterRemoved].filter(null)
    }
    else {
      Charts[filterRemoved].filter(textRemoved);
    }

    dc.redrawAll();

});

var ResetAll = function() {
    dc.filterAll();
    dc.redrawAll();
    filters = {}
};
