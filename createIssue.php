<?php 
/**
 * Abstract class that has 
 * @function Abstract PROTECTED postIssue 
 * @function PUBLIC static getDetailsFromUrl 
 * @function PUBLIC make_api_call 
 * @function PUBLIC static checkInputParams 
 */ 
abstract class CreateIssue 
{
   /**
    * Abstract Function to post the Issue to the Repository 
    * Mandatory for class that inherits it to define this function
    * @param - Array $inputData The input data as passed as arguments to the script
    * @param - String $repositoryOwnerName The Name of the owner of the repository as extracted from the repository URL
    * @param - String $repositoryName The Name of the repository as extracted from the repository URL
    * @return - Array The result of the API call
    */ 
   abstract protected function postIssue($inputData, $repositoryOwnerName, $repositoryName);
   
   /**
    * Function to get the domain from the URL entered in the arguments
    * @param - String $repositoryUrl The URL of the repository where issue is to be posted
    * @return - Array With indexes as the domain, Username and the repository name
    */ 
   public static function getDetailsFromUrl($repositoryUrl) {
     $urlDetails = parse_url($repositoryUrl);
     return $urlDetails;
   }
   
   /**
    * Function to make api call to the URL passed as parameter
    * @param - String $url The URL to which api call is to be made
    * @param - String $username The username of the user in the repository site
    * @param - String $password The password of the user
    * @param - Array $postData The post data that is to be passed to the api (default empty array)
    * @param - Array $jsonDecodeData Checks if the post data need to be json decoded or not 
    * @return - Array 
    */ 
   public function make_api_call($url, $username, $password, $postData = array(), $jsonDecodeData = FALSE) {
     try {
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Gaurav Handa Issues Creation');
     if ($jsonDecodeData) {
       $postData = json_encode($postData);
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
     } catch(Excepition $e){
       $e = new Exception("There was some error in connecting to {$url}. Try again after sometime");
       throw $e;
     }
   }
   
   /**
    * Function Name: checkInputParams()
    * Function to check the required details are passed as arguments to the script in the command line
    * If no arguments are passed, then asks users to enter each argument manually one by one
    * Script Requires following parameters: 
    * username - Name of the user posting the issue
    * password - Password of the user posting the issue
    * repositoryUrl - The URL of the repository where issue is to be posted
    * issueTitle - The Title of the issue
    * stepsToReproduce - The Steps required to reproduce the issue
    * All details are mandatory
    */ 
   public static function checkInputParams() {
     //An array to store all the input parameters
     $inputArray = array();
     
     //Checks if the User name is passed as an argument. 
     //If not, asks user to enter username
     if ( ! isset($_SERVER['argv'][2])) {
       do {
         $handle = fopen ('php://stdin', 'r');
         $input = trim(fgets($handle));
         if ( ! $input) {
           echo "Enter Username: ";
         }
       } while( ! $input);
       $inputArray['username'] = trim($input);
     } else {
       $inputArray['username'] = trim($_SERVER['argv'][2]);
     }

     //Checks if the Password is passed as an argument. 
     //If not, asks user to enter password
     if ( ! isset($_SERVER['argv'][4])) {
       do {
         $handle = fopen ('php://stdin', 'r');
         $input = trim(fgets($handle));
         if ( ! $input) {
           echo "Enter Password: ";
         }
       } while( ! $input);
       $inputArray['password'] = trim($input);
     } else {
       $inputArray['password'] = trim($_SERVER['argv'][4]);
     }

     //Checks if the Repository URL is passed as an argument. 
     //If not, asks user to enter Repository URL
     if ( ! isset($_SERVER['argv'][5])) {
       do {
         $handle = fopen ('php://stdin', 'r');
         $input = trim(fgets($handle));
         if ( ! $input) {
           echo 'Enter Repository URL: ';
         }
       } while( ! $input);
       $inputArray['repositoryUrl'] = trim($input);
     } else {
       $inputArray['repositoryUrl'] = trim($_SERVER['argv'][5]);
     }

     //Checks if the Title is passed as an argument. 
     //If not, asks user to enter password
     if ( ! isset($_SERVER['argv'][6])) {
       do {
         $handle = fopen ('php://stdin', 'r');
         $input = trim(fgets($handle));
         if ( ! $input) {
           echo "Enter Title for the issue: ";
         }
       } while( ! $input);
       $inputArray['issueTitle'] = trim($input);
     } else {
       $inputArray['issueTitle'] = trim($_SERVER['argv'][6]);
     }

     //Checks if steps to reproduce the issue are passed as an argument. 
     //If not, asks user to enter them
     if ( ! isset($_SERVER['argv'][7])) {
       do {
         $handle = fopen ('php://stdin', 'r');
         $input = trim(fgets($handle));
         if ( ! $input) {
           echo "Enter steps to reproduce the issue: ";
         }
       } while( ! $input);
       $inputArray['stepsToReproduce'] = trim($input);
     } else {
       $inputArray['stepsToReproduce'] = trim($_SERVER['argv'][7]);
     }
   return $inputArray;
 }
}

/**
* Class Name: CreateGitHubIssue
* Child Class that extends CreateIssue Abstract Class
* Used for posting Issue to GitHub
* @function PUBLIC postIssue  
*/ 
class CreateGitHubIssue extends CreateIssue
{
 /**
  * Function to post the Issue to the GitHub Repository 
  * @param - Array $inputData The input data as paased as arguments to the script
  * @param - String $repositoryOwnerName The Name of the owner of the repository as extracted from the repository URL
  * @param - String $repositoryName The Name of the repository as extracted from the repository URL
  * @return - Array The result of the API call
  */ 
 public function postIssue($inputData, $repositoryOwnerName, $repositoryName) {
   $gitHubUrlToPostIssue = "https://api.github.com/repos/{$repositoryOwnerName}/{$repositoryName}/issues";
   // Creates an array of post data
   $postData = array(
       'title' => $inputData['issueTitle'],
       'body' => $inputData['stepsToReproduce']
   );
   // Calls the function to make api call to the GitHub and create a new post
   $postIssueResultEnocoded = $this->make_api_call($gitHubUrlToPostIssue, $inputData['username'], $inputData['password'], $postData, TRUE);
   $postIssueResult = json_decode($postIssueResultEnocoded, TRUE);
   $result = array();
   if (isset($postIssueResult['url']) && $postIssueResult['url']) {
     $result = array(
         'result' => TRUE,
         'message' => "\nSuccessfully posted issue to {$inputData['repositoryUrl']} \nYou can check the issue by visiting this link: {$inputData['repositoryUrl']}/issues/{$postIssueResult['number']}"
     );
   } else {
     $result = array(
         'result' => FALSE,
         'message' => "\nThere was some error in posting the issue to {$inputData['repositoryUrl']}"
     );
   }
   return $result;
 }
}

/**
* Class Name: CreateBitBucketIssue
* Child Class that extends CreateIssue Abstract Class
* Used for posting Issue to BitBucket
* @function PUBLIC postIssue  
*/ 
class CreateBitBucketIssue extends CreateIssue
{
 /**
  * Function to post the Issue to the BitBucket Repository 
  * @param - Array $inputData The input data as paased as arguments to the script
  * @param - String $repositoryOwnerName The Name of the owner of the repository as extracted from the repository URL
  * @param - String $repositoryName The Name of the repository as extracted from the repository URL
  * @return - Array The result of the API call
  */ 
 public function postIssue($inputData, $repositoryOwnerName, $repositoryName) {
   $bitBucketUrlToPostIssue = "https://bitbucket.org/api/1.0/repositories/{$repositoryOwnerName}/{$repositoryName}/issues";
   // Creates an array of post data
   $postData = array(
       'title' => $inputData['issueTitle'],
       'content' => $inputData['stepsToReproduce']
   );
   // Calls the function to make api call to the BitBucket and create a new post
   $postIssueResultEnocoded = $this->make_api_call($bitBucketUrlToPostIssue, $inputData['username'], $inputData['password'], $postData, FALSE);
   $postIssueResult = json_decode($postIssueResultEnocoded, TRUE);
   $result = array();
   if (isset($postIssueResult['local_id']) && $postIssueResult['local_id']) {
     $result = array(
         'result' => TRUE,
         'message' => "\nSuccessfully posted issue to {$inputData['repositoryUrl']} \nYou can check the issue by visiting this link: {$inputData['repositoryUrl']}/issue/{$postIssueResult['local_id']}"
     );
   } else {
     $result = array(
         'result' => FALSE,
         'message' => "\nThere was some error in posting the issue to {$inputData['repositoryUrl']}"
     );
   }
   return $result;
 }
}

//Main Code
try {
 $inputData = CreateIssue::checkInputParams();
 $repositoryUrlDetails = CreateIssue::getDetailsFromUrl($inputData['repositoryUrl']);

 if (isset($repositoryUrlDetails['host']) && ! empty($repositoryUrlDetails['host'])) {
   $urlPath = explode("/", $repositoryUrlDetails['path']);
   $repositoryOwnerName = (isset($urlPath[1]) && ! empty($urlPath[1])) ? $urlPath[1] : FALSE;
   $repositoryName = (isset($urlPath[2]) && ! empty($urlPath[2])) ? $urlPath[2] : FALSE;

   if ( !($repositoryOwnerName) || !($repositoryName)) {
     throw new Exception("Repository Name and the User name in the repository URL are mandatory.");
   }

   if ($repositoryUrlDetails['host'] == 'github.com' || $repositoryUrlDetails['host'] == 'www.github.com') {
     $postIssueObject = new CreateGitHubIssue();
   } else if ($repositoryUrlDetails['host'] == 'bitbucket.org' || $repositoryUrlDetails['host'] == 'www.bitbucket.org') {
     $postIssueObject = new CreateBitBucketIssue();
   } else {
     throw new Exception("Repository URL seems to be incorrect. You can currently only post issues to bitbucket.org and github.com");
   }
   $resultPostIssue = $postIssueObject->postIssue($inputData, $repositoryOwnerName, $repositoryName);
   if (isset($resultPostIssue['result']) && $resultPostIssue['result']) {
     echo $resultPostIssue['message'];
   } else {
     $e = new Exception($resultPostIssue['message']);
     throw $e;
   }
 } else {
   $e = new Exception("The URL entered is invalid. Run the script again with correct values to continue.");
   throw $e;
 }
} catch(Exception $e){
echo "Error Occured. ".$e->getMessage();
exit();
}
exit();
?>
