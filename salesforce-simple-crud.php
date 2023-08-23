<?php

    $instance_url = getToken()['instance_url'];
    $access_token = getToken()['access_token'];
    $txtToFind = isset($_GET['find']) ? $_GET['find'] : null;
    $txtToFind = isset($_GET['find']) ? $_GET['find'] : null;

    echo "<form action='salesforce-simple-crud.php'><p>Buscar <input type='text' name='find' /> <input type='submit' value='Enviar' /></p></form>";

    if (isset($_GET['find'])) find($txtToFind , $instance_url, $access_token);

    // get_record("e", "Account", $instance_url, $access_token);

    // create_account("Sakshi","00324234000", $instance_url, $access_token);

    // update_account("0010Q00001k4oq2QAA", "Test update", "61616616", $instance_url, $access_token);

    // delete_account("0010Q00001k4qGBQAY", $instance_url, $access_token);

    function getToken(){

        $token_url ="https://test.salesforce.com/services/oauth2/token";
        $params =
        "grant_type=password"
        . "&client_id="
        . "&client_secret="
        . "&username="
        . "&password=";

        $curl = curl_init($token_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ( $status != 200 ){
                    die("Error: call to token URL $token_url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
                }
        curl_close($curl);

        $response = json_decode($json_response, true);

        return $response;
    }

	function get_record($id, $recordType, $instance_url , $access_token) {

		$url = $instance_url."/services/data/v58.0/sobjects/".$recordType."/".$id;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array("Authorization: Bearer $access_token"));
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ( $status != 200 ) {
				    die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
				}
			curl_close($curl);
			$response = json_decode($json_response, true);
			$account = array(
                    "Id"=>$response['Id'],
                    "Name" => isset($response['Name']) ? $response['Name'] : "",
                    "Record type" => $recordType
                );
				foreach($account as $x => $x_value) {
						echo "<b>" . $x . ":</b> " . $x_value . "<br>";
					}
            // foreach ($response['sobjects'] as $sobject) {
            //     print_r($response['sobjects']);
            // }
            echo "<br>";
            // print_r($response);
	}

    function create_account($name, $phone, $instance_url, $access_token) {
        $url = "$instance_url/services/data/v58.0/sobjects/Account/";
        $content = json_encode(array("Name" => $name, "Phone" =>$phone));
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array("Authorization: Bearer $access_token","Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ( $status != 201 ) 
            {
                die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
            }
            echo "succesfully creating account<br/><br/>";
            curl_close($curl);
        $response = json_decode($json_response, true);
        $id = $response["id"];
        echo "New record id $id,$phone<br/><br/>";
    }

    function update_account($id, $new_name, $phone, $instance_url, $access_token) {
        $url = "$instance_url/services/data/v58.0/sobjects/Account/$id";
        $content = json_encode(array("Name" => $new_name, "Phone" => $phone));
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: OAuth $access_token", "Content-type: application/json"));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 204 ) {
            die("Error: call to URL $url failed with status $status, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        echo "HTTP status $status updating account";
        curl_close($curl);
    }
         
    function delete_account($id, $instance_url, $access_token) {
        
        $url = "$instance_url/services/data/v58.0/sobjects/Account/$id";
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: OAuth $access_token"));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 204 ) {
            die("Error: call to URL $url failed with status $status, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        echo "HTTP status $status deleting account";
        curl_close($curl);
    }

    function find($txtToFind, $instance_url , $access_token) {

        // $select = "SELECT+id+FROM+Account+LIMIT+10";
        // $url = $instance_url."/services/data/v58.0/query?q=".$select;
        $url = $instance_url."/services/data/v58.0/search/?q=FIND+%7B" . $txtToFind . "%7D+LIMIT+10";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array("Authorization: Bearer $access_token"));
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ( $status != 200 ) {
                    die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
                }
            curl_close($curl);
            $response = json_decode($json_response, true);
            foreach ($response['searchRecords'] as $record) {
                get_record($record['Id'], $record['attributes']['type'], $instance_url, $access_token);
            }
            // print_r($response);
            return $response;
    }