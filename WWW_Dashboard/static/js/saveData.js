window.saveData = function() {
    var header = [
        'DATA_AS_OF'
        , 'CLUSTER_ID'
        , 'AGENCY'
        , 'PARTNER'
        , 'PARTNER2'
        , 'DONOR'
        , 'ADMIN1_ID'
        , 'ADMIN2_ID'
        , 'area_type'
        , 'STATUS_ACTIVITY'
        , 'ACTIVITY'
        , 'START_ACTIVITY'
        , 'END_ACTIVITY'
        , 'BENEF_HH'
        , 'BENEF_IND'
        , 'Oblast_Name'
        , 'Raion_Name'
    ]

    var data = cf.dataasof.top(Infinity)

    data = data.map(function(record) {
        return [
            record['DATA_AS_OF']
            , record['CLUSTER_ID']
            , record['AGENCY']
            , record['PARTNER']
            , record['PARTNER2']
            , record['DONOR']
            , record['ADMIN1_ID']
            , record['ADMIN2_ID']
            , record['area_type']
            , record['STATUS_ACTIVITY']
            , record['ACTIVITY']
            , record['START_ACTIVITY']
            , record['END_ACTIVITY']
            , record['BENEF_HH']
            , record['BENEF_IND']
            , record['Oblast_Name']
            , record['Raion_Name']
        ]
    })

    data.unshift(header)

    var res = d3.csv.formatRows(data)

    var blob = new Blob([res], {type: "text/csv;charset=utf-8"})

    saveAs(blob, 'ukraine-3w-oblast-dataset-' + (new Date()).getTime() + '.csv')
}