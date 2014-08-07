<?php 
/**
 * CommandLineScript
 *
 *
 * This is free command line script; you can redistribute it and/or
 * extend it as per requirement.
 *
 *
 * @author   Gaurav Handa
 * @link     https://github.com/gauravhanda25/issuesCreationScript
 *
 */
 
 
/**
 * abstract Class Issue
 *
 * @function abstract protected create_issue
 * @function public do_api_call
 * @function public static check_params
 * @function public static fetch_url_data
 * 
 */
abstract class Issue 
{
	/**
	 * Create Issue abstract function to create issue on repository.
	 * Mandatory for class that inherits it to define this function
	 * @param - Array $inputArgs The input data as passed as arguments to the script
	 * @param - String $ownerName The Name of the owner of the repository as extracted from the repository URL
 	 * @param - String $repositoryName The Name of the repository as extracted from the repository URL
	 * @return - Array The result of the API call
	 */
	abstract protected function create_issue($inputArgs, $ownerName, $repositoryName);
	
	/**
    * Fetch Url Data  static function to fetch the domain from the URL entered in the args
    * @param - String $repositoryUrl The URL of the repository where issue is to be posted
    * @return - Array With indexes as the domain, Username and the repository name
    */ 
	public static function fetch_url_data($repositoryUrl)
	{
		$urlDetails = parse_url($repositoryUrl);
		return $urlDetails;
	}
	
	/**
    * Do Api Call to call api 
    * @param - String $url The URL to which api call is to be made
    * @param - String $username username of the user in the repository site
    * @param - String $password password of the user
    * @param - Array $postData data to post(default empty array)
    * @param - Array $jsonDecodeData Checks if the post data need to be json decoded or not 
    * @return - Array containing response from the api
    * @throws - api connection error
    */ 
	public function do_api_call($url, $username, $password, $postData = array(), $jsonDecodeData = FALSE)
	{
		try	{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if ($jsonDecodeData) {
				$postData = json_encode($postData);
				curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Gaurav Handa Creation Script');
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($postData))); 
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
			$result_encoded = curl_exec($ch);
			curl_close($ch);
			return $result_encoded;
		}	catch(Excepition $e)	{
			$e = new Exception("There was some error in connecting to {$url}. Try again after sometime");
			throw $e;
		}
	}
	
	/**
    * Check Params
    * static function to check the required details are passed as arguments to the script in the command line
    * If no arguments are passed, then asks users to enter each argument manually one by one
    * Script Requires following parameters: 
    * username - Name of the user posting the issue
    * password - Password of the user posting the issue
    * repositoryUrl - The URL of the repository where issue is to be posted
    * issueTitle - The Title of the issue
    * stepsToReproduce - The Steps required to reproduce the issue
    * All parameters are mandatory
    */    
	public static function check_params()
	{
   		 //An array to store all the input parameters
		 $inputArray = array();

		 if ( ! isset($_SERVER['argv'][2])) {      
			$err = "Enter Username: ";
			$std_err = fopen("php://stderr", "w");
			fwrite($std_err, $err);
			fclose($std_err);
			do	{
				$handle = fopen ('php://stdin', 'r');
				$input = trim(fgets($handle));
				if ( ! $input)	{
					$err = "Enter Username: ";
					$std_err = fopen("php://stderr", "w");
					fwrite($std_err, $err);
					fclose($std_err);
					
				}
			}	while( ! $input);
			$inputArray['username'] = trim($input);

		 } else {
			$inputArray['username'] = trim($_SERVER['argv'][2]);
		 }

		 if ( ! isset($_SERVER['argv'][4]))	 {
			$err = "Enter Password: ";
			$std_err = fopen("php://stderr", "w");
			fwrite($std_err, $err);
			fclose($std_err);
			do	{
				$handle = fopen ('php://stdin', 'r');
				$input = trim(fgets($handle));
				if ( ! $input)	{
					$err = "Enter Password: ";
					$std_err = fopen("php://stderr", "w");
					fwrite($std_err, $err);
					fclose($std_err);
				}
		   }	while( ! $input);
		   $inputArray['password'] = trim($input);
		 } else	 {
		   $inputArray['password'] = trim($_SERVER['argv'][4]);
		 }

		 if ( ! isset($_SERVER['argv'][5])) {
			$err = 'Enter Repository URL: ';
			$std_err = fopen("php://stderr", "w");
			fwrite($std_err, $err);
			fclose($std_err);
			do	{
				$handle = fopen ('php://stdin', 'r');
				$input = trim(fgets($handle));
				if ( ! $input)	{
					$err = 'Enter Repository URL: ';
					$std_err = fopen("php://stderr", "w");
					fwrite($std_err, $err);
					fclose($std_err);
					
				}
			}	while( ! $input);
			$inputArray['repositoryUrl'] = trim($input);
		 } else {
			$inputArray['repositoryUrl'] = trim($_SERVER['argv'][5]);
		 }

		 if ( ! isset($_SERVER['argv'][6])) {
			$err = "Enter Title for the issue: ";
			$std_err = fopen("php://stderr", "w");
			fwrite($std_err, $err);
			fclose($std_err);
			do	{
				$handle = fopen ('php://stdin', 'r');
				$input = trim(fgets($handle));
				if ( ! $input)	{
					$err = "Enter Title for the issue: ";
					$std_err = fopen("php://stderr", "w");
					fwrite($std_err, $err);
					fclose($std_err);
				}
			}	while( ! $input);
			$inputArray['issueTitle'] = trim($input);
		 } else {
			$inputArray['issueTitle'] = trim($_SERVER['argv'][6]);
		 }

		 if ( ! isset($_SERVER['argv'][7])) {
			$err = "Enter content of issue: ";
			$std_err = fopen("php://stderr", "w");
			fwrite($std_err, $err);
			fclose($std_err);
			do	{
				$handle = fopen ('php://stdin', 'r');
				$input = trim(fgets($handle));
				if ( ! $input)	{
					$err = "Enter content of issue: ";
					$std_err = fopen("php://stderr", "w");
					fwrite($std_err, $err);
					fclose($std_err);
				}
			}	while( ! $input);
			$inputArray['stepsToReproduce'] = trim($input);
		 } else {
			$inputArray['stepsToReproduce'] = trim($_SERVER['argv'][7]);
		 }
		return $inputArray;
	}
}

/**
 * GitHub
 *
 * child class that extends Absract Class Issue for creating an issue
 * @function PUBLIC create_issue
 */
class GitHub extends Issue
{
	/**
	* Function to create the Issue to the GitHub Repository 
	* @param - Array $inputArgs The input data as passed as arguments to the script
	* @param - String $ownerName The Name of the owner of the repository as extracted from the repository URL
	* @param - String $repositoryName The Name of the repository as extracted from the repository URL
	* @return - Array The result of the API call
	*/
	public function create_issue($inputArgs, $ownerName, $repositoryName)
	{
		$gitHubUrlToPostIssue = "https://api.github.com/repos/{$ownerName}/{$repositoryName}/issues";
		
		// Creates an array to post data
		$postData = array(
						   'title' => $inputArgs['issueTitle'],
						   'body' => $inputArgs['stepsToReproduce']
						);
		// Calls the function to post an issue to GitHub Repository
		$postIssueResultEnocoded = $this->do_api_call($gitHubUrlToPostIssue, $inputArgs['username'], $inputArgs['password'], $postData, TRUE);
		$postIssueResult = json_decode($postIssueResultEnocoded, TRUE);
		$result = array();
		if (!empty($postIssueResult['url']))	{
			 $result = array(
				 'result' => TRUE,
				 'message' => "\nSuccessfully posted issue to {$inputArgs['repositoryUrl']}"
			 );
		}	else	{
			 $result = array(
				 'result' => FALSE,
				 'message' => "\nError in posting the issue to {$inputArgs['repositoryUrl']}"
			 );
		}
		return $result;
	}
}

/**
 * BitBucket
 *
 * child class that extends Absract Class Issue for creating an issue
 * @function PUBLIC create_issue
 */
class BitBucket extends Issue
{
	/**
	* Function to create the Issue to the BitBucket Repository 
	* @param - Array $inputArgs The input data as passed as arguments to the script
	* @param - String $ownerName The Name of the owner of the repository as extracted from the repository URL
	* @param - String $repositoryName The Name of the repository as extracted from the repository URL
	* @return - Array The result of the API call
	*/
	public function create_issue($inputArgs, $ownerName, $repositoryName)
	{
		$bitBucketUrl = "https://bitbucket.org/api/1.0/repositories/{$ownerName}/{$repositoryName}/issues";
		
		// Creates an array to post data
		$postData = array(
						   'title' => $inputArgs['issueTitle'],
						   'content' => $inputArgs['stepsToReproduce']
						);
		
		// Calls the function to post an issue to BitBucket Repository
		$postIssueResultEnocoded = $this->do_api_call($bitBucketUrl, $inputArgs['username'], $inputArgs['password'], $postData, FALSE);
		$postIssueResult = json_decode($postIssueResultEnocoded, TRUE);
		$result = array();
		if (!empty($postIssueResult['local_id']))	{
			$result = array(
							 'result' => TRUE,
							 'message' => "\nSuccessfully posted issue to {$inputArgs['repositoryUrl']}"
						 );
		}	else	{
			$result = array(
					 'result' => FALSE,
					 'message' => "\n Error in posting the issue to {$inputArgs['repositoryUrl']} \n"
				 );
		}
		return $result;
	}
}

//Start of execution
try {
	$inputArgs = Issue::check_params();
	$repositoryUrlDetails = Issue::fetch_url_data($inputArgs['repositoryUrl']);

	if (isset($repositoryUrlDetails['host']) && ! empty($repositoryUrlDetails['host']))	{
		$urlPath = explode("/", $repositoryUrlDetails['path']);
		$ownerName = (isset($urlPath[1]) && ! empty($urlPath[1])) ? $urlPath[1] : FALSE;
		$repositoryName = (isset($urlPath[2]) && ! empty($urlPath[2])) ? $urlPath[2] : FALSE;

		if ( !($ownerName) || !($repositoryName))	{
			throw new Exception("Repository Name and the Username in the repository URL are mandatory.".PHP_EOL);
		}

		if ($repositoryUrlDetails['host'] == 'github.com' || $repositoryUrlDetails['host'] == 'www.github.com')	{
			$postIssueObject = new GitHub();
		}	else if ($repositoryUrlDetails['host'] == 'bitbucket.org' || $repositoryUrlDetails['host'] == 'www.bitbucket.org')	{
			$postIssueObject = new BitBucket();
		}	else	{
			throw new Exception("Incorrect repository url. You can only post issues to bitbucket.org and github.com for now".PHP_EOL);
		}
		
		$resultPostIssue = $postIssueObject->create_issue($inputArgs, $ownerName, $repositoryName);
		if(isset($resultPostIssue['result']) && $resultPostIssue['result'])	{
			echo $resultPostIssue['message'];
		}	else	{
			$e = new Exception($resultPostIssue['message']);
			throw $e;
		}
	}	else	{
		$e = new Exception("Invalid URL. Please enter valid URL.".PHP_EOL);
		throw $e;
	}
}	catch(Exception $e)	{
	echo "Error Occured. ".$e->getMessage();
	exit();
}
exit();
?>
