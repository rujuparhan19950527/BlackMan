<?php defined("ABSPATH") || die("!"); ?>
<html>
    <head>
        <title>Chapter Sorter - <?php echo get_bloginfo("name"); ?></title>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
		    <script src="https://cdn.jsdelivr.net/npm/xss@1.0.14/dist/xss.min.js"></script>
        <style>
            #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
            #sortable li {cursor: move;margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; border:1px solid #ddd;}
            #sortable li span { position: absolute; margin-left: -1.3em; }
            #preview, iframe{
              width:100%;
              height:300px;
            }
            #save, .hide{
              display:none;
            }
            .sortable-drag{
              color:red;
            }
            .sortable-ghost{
              color:green;
              opacity: 0.4;
            }
            #init{
              padding: 10px;
              border: 1px solid #ddd;
              margin-bottom: 10px;
              color:green;
            }
            #save{
              margin-top: 10px;
              margin-left: 3px;
              padding: 10px;
              border: 1px solid #ddd;
              background: #eee;
              cursor: pointer;
            }
        </style>
    </head>
    <body>
	<a href="<?php echo get_site_url(); ?>/wp-admin/">Dashboard</a>
	<a href="?access_token=<?php echo $access_token; ?>&new=1">New Task</a>
    <?php 
    if (is_object($mangaData) && $mangaData && is_numeric($mangaID) && $mangaID) { ?>
		<?php include get_theme_file_path("inc/parts/sort-chapters/list.php"); ?>
    <?php } else { ?>
		<?php include get_theme_file_path("inc/parts/sort-chapters/init.php"); ?>
    <?php } ?>
    </body>
</html>