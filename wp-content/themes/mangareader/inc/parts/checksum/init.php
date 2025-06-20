<?php defined("ABSPATH") || die("!"); ?>
<html>

<head>
    <title>Checksum</title>
    <style>
        #modified,
        #ok,
        #unknown,
        #nocheck {
            display: none;
        }

        legend {}

        .initbtn {
            width: 100%;
            text-align: center;
        }

        .initial-button {
            --b: 3px;
            --s: .45em;
            --color: #373B44;
            padding: calc(.5em + var(--s)) calc(.9em + var(--s));
            color: var(--color);
            --_p: var(--s);
            background:
                conic-gradient(from 90deg at var(--b) var(--b), #0000 90deg, var(--color) 0) var(--_p) var(--_p)/calc(100% - var(--b) - 2*var(--_p)) calc(100% - var(--b) - 2*var(--_p));
            transition: .3s linear, color 0s, background-color 0s;
            outline: var(--b) solid #0000;
            outline-offset: .6em;
            font-size: 16px;

            border: 0;

            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .initial-button:hover,
        .initial-button:focus-visible {
            --_p: 0px;
            outline-color: var(--color);
            outline-offset: .05em;
        }

        .initial-button:active {
            background: var(--color);
            color: #fff;
        }
        fieldset{
            margin-top: 10px;
        }
        #inf{
            width: 100%;
            text-align: left;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>

<body>
    <div class="initbtn">
        <button class="initial-button" onclick="check();">CHECK</button>
    </div>
    <div id="inf"></div>
    <div id="files">
        <fieldset id="nocheck">
            <legend>Not Checked</legend>
            <div id="nocheck-pool"></div>
        </fieldset>
        <fieldset id="modified">
            <legend>Modified</legend>
            <div id="modified-pool"></div>
        </fieldset>
        <fieldset id="unknown">
            <legend>Not Found</legend>
            <div id="unknown-pool">
                <fieldset>
                    <legend>Exists in the Original Package, But Not Found in Your Theme</legend>
                    <div id="unknown-pool-package"></div>
                </fieldset>
                <fieldset>
                    <legend>Exists in Your Theme, But Not Found in the Original Package</legend>
                    <div id="unknown-pool-local"></div>
                </fieldset>
            </div>
        </fieldset>
        <fieldset id="ok">
            <legend>Not Modified</legend>
            <div id="ok-pool"></div>
        </fieldset>
    </div>
    <script>
        let _conf = {
            'product': '<?php echo $product_version; ?>',
            'product_version': '<?php echo $version; ?>',
            'product_name': '<?php echo $product; ?>',
            'dirname': '<?php echo $dirname; ?>',
            'original_dirname': '<?php echo $original_dirname; ?>',
            'core_hash': <?php echo json_encode($core_hash); ?>,
            'checksum_provider': '<?php echo $checksum_provider; ?>',
            'hashes': <?php echo json_encode($hashes); ?>,
            'skip': <?php echo json_encode($skip_scan); ?>,
        };
        function check() {
            jQuery('.initial-button').html('Loading...');
            jQuery.get(_conf.checksum_provider).done(function (result) {
                if (!result) {
                    jQuery('#inf').html("Checksum Server Error!");
                    jQuery('.initial-button').css('visibility', 'hidden');
                    return;
                }
                if (result.error && [0, 1, 2].includes(parseInt(result.error)) == false) {
                    jQuery('#inf').html(result.data);
                    jQuery('.initial-button').css('visibility', 'hidden');
                    return;
                }
                if (result.error == 0){
                    jQuery('#inf').html(`<p>Checking against your original Package</p>`);
                }else if(result.error == 1){
                    jQuery('#inf').html(`<p>Your Original Package is not available on our server, Checking against ${_conf.product}, some files will not be checked</p>`);
                }else if(result.error == 2){
                    jQuery('#inf').html(`<p>Your Original Package or Package with the same version as yours is not available on our server, Checking against ${result.from}, some files will not be checked and many files will be marked as modified</p>`);
                }
                var found = [];
                for (var i of result.data) {
                    i.file = i.file.replace(_conf.original_dirname, _conf.dirname);
                    found.push(i.file);
                    if (_conf.skip.includes(i.file) && parseInt(result.error) != 0) {
                        jQuery('#nocheck-pool').append(`<div>${i.file}</div>`);
                        continue;
                    }
                    var hash_item = _conf.hashes[i.file];
                    if (i.file.endsWith("/") && i.hash === '00000000' && _conf.dirname + '/' === i.file) {
                        jQuery('#ok-pool').append(`<div>✓ ${i.file}</div>`);
                        continue;
                    }
                    //found in Package but not in local
                    if (!hash_item) {
                        jQuery('#unknown-pool-package').append(`<div>? ${i.file}</div>`);
                        continue;
                    }
                    if (hash_item.hash != i.hash) {
                        jQuery('#modified-pool').append(`<div>⚠ ${i.file}</div>`);
                        continue;
                    }
                    if (hash_item.hash == i.hash) {
                        jQuery('#ok-pool').append(`<div>✓ ${i.file}</div>`);
                        continue;
                    }
                }
                var localItem = Object.keys(_conf.hashes).filter(x => !found.includes(x));
                for (var i of localItem) {
                    jQuery('#unknown-pool-local').append(`<div>? ${i}</div>`);
                }

                if (!jQuery('#ok-pool').html().trim()) jQuery('#ok-pool').html("-");
                if (!jQuery('#unknown-pool-package').html().trim()) jQuery('#unknown-pool-package').html("-");
                if (!jQuery('#modified-pool').html().trim()) jQuery('#modified-pool').html("-");
                if (!jQuery('#unknown-pool-local').html().trim()) jQuery('#unknown-pool-local').html("-");
                if (!jQuery('#nocheck-pool').html().trim()) jQuery('#nocheck-pool').html("-");

                jQuery('#ok').show();
                jQuery('#modified').show();
                jQuery('#unknown').show();
                jQuery('#nocheck').show();
                jQuery('.initial-button').css('visibility', 'hidden');
            })
            .fail(function () {
                jQuery('.initial-button').css('visibility', 'hidden');
                alert('unknown error');
            });
        }

    </script>
</body>

</html>