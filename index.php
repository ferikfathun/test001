<head>
    <script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <script>
        var searchElements = [];
        function submit_form() {
            var inputs = $(".inputvalue");
            for(var i = 0; i < inputs.length; i++) {
                if($(inputs[i]).val().trim() !="")
                    searchElements.push($(inputs[i]).val());
                else if (inputs.size()!=1)
                    $(inputs[i]).remove();
            }
            $("#searchStrings").val(JSON.stringify(searchElements));
            $("#searchForum").submit();
        }
    </script>
</head>
<form action="" method="POST" id="searchForum">
    <table>
        <tbody>
        <tr>
            <td><label for="search">String </label></td>
            <td id="searchBoxes" >
                <input hidden id="searchStrings" name="searchStrings" />
                <input id="search" class="inputvalue" style="margin-right:-3px" type="text" name="search" placeholder="Enter your string" />
            </td>
        </tr>
        <tr>
            <td><input type="button" onclick="submit_form();" value="Search" /></td>
        </tr>
        </tbody>
    </table>
</form>
<?php
define("ROOT_DIR", __DIR__);

$path_to_check = ROOT_DIR . '/testDir';
$count = 0;
function getDirContents($dir, &$results = array()) {
    global $count;
    $searchStrings = array();
    if(isset($_POST['searchStrings']))
        $searchStrings = json_decode($_POST['searchStrings'], true);

    echo "<script>var elm = document.getElementById('search');elm.value='$_POST[search]';</script>";

    $string = $_POST['search'];
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {

            $content = file_get_contents($path);
            $label = null;
            $fileName = basename($path, '?' . $_SERVER['QUERY_STRING']);
            $type = null;
            $types = array();
            if(isset($_POST['type']) && str_replace(' ','', $_POST['type']) != "") {
                $types = explode(',', str_replace(' ','', $_POST['type']));
            }
            foreach ($searchStrings as $tmp) {
                if($types != null){
                    if (in_array(substr(strrchr($fileName, '.'), 1), $types)) {
                        if (strpos($content, $tmp) !== false) {
                            $label = $label."&nbsp&nbsp <label class='label'>".$tmp."</label>&nbsp&nbsp<label class='label'>.".substr(strrchr($fileName, '.'), 1)."&nbsp&nbsp</label>";
                            $count++;
                        }
                    }
                } else {
                    if (strpos($content, $tmp) !== false) {
                        $label = $label."&nbsp&nbsp <label class='label'>".$tmp."</label>&nbsp&nbsp";
                        $count++;
                    }
                }
            }
        }
        else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }
}
getDirContents($path_to_check);
if(!empty($_POST['search']))
    echo $_POST['search']. ' ' .$count;