<?php defined("ABSPATH") || die("!"); ?>
<p>
    Please BackUp Your Database before using this feature
</p>
<p>
    This Chapter List is Already Sorted (DESC) by Default, You Can Rearrange Each Chapter order by Drag and Drop it.
</p>
<p>
    This feature will change post date of every chapter bellow
</p>
<p>
    Note: Latest Chapter must be on Top
</p>

<p id="init">Loading... 0%</p>

<ul id="sortable" width="100%"></ul>

<?php if ($hasChapter) { ?>
    <button id="save" onclick="saveMeNow();">SAVE</button>
<?php } else { ?>
    NO CHAPTER
<?php } ?>

<script>
    const fetch_per_page = 100;
    const series_id = <?php echo intval($mangaID); ?>;
    const ids = <?php echo json_encode($chapters); ?>;
    var chap_items = {};
    var keys = [];
    var sortable;
    
    function delayedForEach(arr, callback, delay, completionCallback) {
        let index = 0;

        function iterate() {
        if (index < arr.length) {
            callback(arr[index], index, arr);
            index++;
            setTimeout(iterate, delay);
        } else {
            if (completionCallback) {
            completionCallback();
            }
        }
        }

        iterate();
    }
    function naturalSort(a, b) {
        return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
    }
    function fetchChapters(aid, ondone){
        if ( ! aid) return ondone();
        if (aid.length < 1) return ondone();
        var idsi = aid.splice(0, fetch_per_page);
        $.post("?act=get-chapters&access_token=<?php echo $access_token; ?>", {
            "ids": idsi,
        }).done(function(d){

            if ( ! d) {
                $("#init").html("<span style='color:red'>Error Getting Chapter list</span>");
                return;
            }
            if (typeof d !== "object") {
                $("#init").html("<span style='color:red'>Error Getting Chapter list</span>");
                return;
            }

            for(var i of Object.keys(d)){
                i = d[i];
                keys.push(i.chapter);
                chap_items[i.chapter] = i;
                
                var perc = (ids.length-aid.length) / ids.length * 100;
                perc = perc.toFixed(2);
                $("#init").html("Loading... " + perc + "%");
            }
            fetchChapters(aid, ondone);
        });
    }
    function createHTML(ondone){
        keys.sort(naturalSort);
        var count = 0;
        delayedForEach(keys, function(v, k){
            var item = chap_items[v];
            var plate = `<li class="ui-state-default sortes" data-id="${filterXSS(item.id)}" data-sort="${filterXSS(item.chapter)}">${filterXSS(item.label)}</li>`;
            $("#sortable").prepend(plate);
            count++;
            var perc = count / ids.length * 100;
            perc = perc.toFixed(2);
            $("#init").html("Generating chapter list... " + perc + "%");
        }, 10, function(){
            $("#init").html("You can now sort the chapters below by drag and drop");
            sortable = new Sortable(document.getElementById('sortable'), {
                animation: 150,
            });
            ondone();
        });
    }
    fetchChapters([...ids], function(){
        createHTML(function(){
            $("#save").show();
        });
    }, 0);
    function saveMeNow(){
        if ($("#save").html() !== "SAVE") return false;
        if ( ! confirm("Are you sure?")) return false;
        saveChanges(0, function(){
            $.post("?act=done&skey=<?php echo $skey; ?>&ukey=<?php echo $ukey;?>&access_token=<?php echo $access_token; ?>", {
                "mid": <?php echo $mangaID; ?>,
            })
            .always(function(){
                $("#init").html("All chapters succesfully updated");
            });
        }, getJsonFromHTML());
    }
    
    function saveChanges(index, cb, json){
        if (json == false){
            $("#init").html("<span style='color:red'>Error Reading Chapter Data</span>");
            return false;
        }
        if (isNaN(index)) index = 0;
        if (index >= json.length) {
            $("#init").html("Updating... 100%");
            $("#save").html("SAVE");
            return cb();
        }
        var perc = index / json.length * 100;
        perc = perc.toFixed(2);
        $("#init").html("Updating... " + perc + "%");
        $("#save").html(perc + "%");
        var item = json[index];
        $.post("?act=update&skey=<?php echo $skey; ?>&ukey=<?php echo $ukey;?>&access_token=<?php echo $access_token; ?>", item)
        .always(function(){
            saveChanges(index+1, cb, json);
        });
    }
    function getJsonFromHTML(){
        var li = jQuery('#sortable li');
        var res = [];
        var allOK = true;
        var order = 1;
        li.each(function(k,v){
            if (allOK === false) return true;
            var tmp = {};
            tmp.id = v.getAttribute('data-id');
            if (isNaN(tmp.id)) {
                allOK = false;
                return true;
            }
            tmp.order = order;

            res.push(tmp);
            order++;
        });
        if (allOK == false) return false;
        return res;
    }
</script>
