<!DOCTYPE html>
<html>
    <head>
        <title>Scraping library with SimpleHtmlDom for CodeIgniter 2.1</title>
        <style>
            td{
                padding: 10px;
                outline: 1px solid black;
            }
        </style>
    </head>
    <body>
        <div>
            <form action="" method="get">
                <span>URL:</span> <input type="text" name="url" id="url"/> <input type="submit" value="GO!">
            </form>
            <br />
            <table>
                <thead>
                    <tr>
                        <td>Path Queried</td>
                        <td>Status Code</td>
                        <td>Unique Images</td>
                        <td>Unique Internal Links</td>
                        <td>Unique External Links</td>
                    </tr>
                </thead>
                <tbody>
            <?php
                foreach($results as $res){
                    echo "<tr>";
                    echo "<td>" . $res['path'] . "</td>";
                    echo "<td>" . $res['statusCode'] . "</td>";
                    echo "<td>" . $res['imgCount'] . "</td>";
                    echo "<td>" . $res['uniqueLinksInt'] . "</td>";
                    echo "<td>" . $res['uniqueLinksExt'] . "</td>";
                    echo "</tr>";
                }
            ?>
                </tbody>
            </table>
            <br />
            <br />
            <table>
                <thead>
                    <tr>
                        <td> Average Page Load (In Seconds) </td>
                        <td> Average Word Count </td>
                        <td> Average Title Length </td>
                    </tr>
                    <?php
                        echo "<td>".round($pageLoad->apl, 2)."</td>";
                        echo "<td>".round($wordCount->awc)."</td>";
                        echo "<td>".round($titleLength->atl)."</td>";
                    ?>
                </thead>
            </table>
        </div>
    </body>
</html>