
X.actionLoaded(function(event, fn, params) {
 
    fn("#idGroup").change(function () {
        let idGroup = fn("#idGroup option:selected").val();
        if(idGroup != ''){
            fn("#metadataLoad").load(
                url_host+ url_root + '?action=metadata&method=getMetadataByGroup&idGroup=' + idGroup
            );
        }else{
            fn("#metadataLoad").html('');
        }


    });
});