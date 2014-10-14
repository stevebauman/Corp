#Corp

##Installation
Add Location to your `composer.json` file.

	"stevebauman/corp": "dev-master"

Then run "composer update" on your project source.

Add the service provider in "app/config/app.php"

	'Stevebauman\Corp\CorpServiceProvider',
	
Add the alias

	'Corp'		=> 'Stevebauman\Corp\Facades\Corp',
	
Publish the config file

	php artisan config:publish stevebauman/corp
	
##Usage

Authenticate an LDAP User

	Corp::auth($username, $password); //Returns true/false (boolean)

Get user information

	Corp::userInfo($username); //Returns large array of user information (array)
	
Get user email

	Corp::userEmail($username); //Returns email only (string)
	
Get a user's full name

	Corp::userFullName($username); //Returns name only (string)

Get a user's group name

	Corp::userGroup($username); //Returns group name only (string)

Get an entire user list

	Corp::userList(); //Returns a large array with each user along with their user information (array)

Get a user list for laravel select list (user ID is value of the select, user's full name is visible portion of the select)

	Corp::userList(); //Returns an array with the users ID and full name (array)
	
Get a computer's information

	Corp::computer($computer_name); //Returns a large array containing Computer group, type, OS, OS version (service pack), OS build (ex. 6.1), and Hostname (array)
	
Check if a computer exists

	Corp::computerExists($computer_name); //Returns true/false (boolean)
	
Get a list of all computers
	
	Corp::allComputers(); //Returns a large array containing the computer name, type, and group. Also returns printers as well
	
##Authenticating with laravel's Auth driver but using LDAP

	if (Corp::auth($username, $password)) { //If Passes LDAP Auth
		
		if(!User::where('username', '=', $username)->first()){ // If web user profile does not exist, create one	
				$user = new User;
				$user->username = $username;
				$user->email = Corp::userEmail($username);
				$user->fullname = Corp::userFullName($username);
				$user->password = Hash::make($password);
				$user->save();
				
		} else{ // If web profile does exist, update the password incase of update on active directory
			$user = User::where('username', '=', $username)->first();
			$user->password = Hash::make($password);
			$user->save();
		}
				
		if(Auth::attempt(array('username'=>$username, 'password'=>$password))){ //If successfully logs in
			return Redirect::route('dashboard.index')
				->with('message', sprintf('Welcome %s. You are now logged in.', Auth::user()->fullname))
				->with('color', 'success');
			}
		}
	} else{
		return Redirect::route('auth.index')
			->with('message', 'Your email/password combination was incorrect')
			->with('color', 'danger')
			->withInput();
	}
