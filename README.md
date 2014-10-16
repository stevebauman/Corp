#Corp
An AdLDAP Helper Package for Larvel 4/5

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

####Accessing AdLDAP object

You can access all adldap functions by using:

    Corp::adldap();

Example:

    Corp::adldap()->group()->info($groupName);
    Corp::adldap()->user()->info($username);
    Corp::adldap()->user()->inGroup($username);
    Corp::adldap()->user()->modify('admin', array('telephone'=>'555 555-5555'));

All functions available [here](https://github.com/adldap/adLDAP/wiki/adLDAP-Developer-API-Reference#functions).

####Change User Password through standard AdLDAP (SSL or TLS required, must enable in package config file)
    Corp::adldap()->user()->password('username', 'password123');

####Change User Password through COM (Windows and COM ext required)
    Corp::com()->user()->password('username', 'password123'); //Returns boolean

####Authenticate an LDAP User

    Corp::auth('username', 'password'); // Returns true/false (boolean)

####Get user information

    $user = Corp::user('username'); // Returns a user object

    echo $user->username;

    echo $user->name;

    echo $user->email

    echo $user->group;

    echo $user->type;

    print_r($user->dn); // Returns distinguished name array

####Get an entire user list

    $users = Corp::users(); // Returns a laravel collection of user objects of all users on current ldap connection

    //Usage for laravel select
    Form::select('users', $users->lists('username', 'name'));

####Get a computer's information

    $computer = Corp::computer('computer name'); // Returns a computer object

    echo $computer->name;

    echo $computer->os->name; // ex. Windows 7 Professional
    echo $computer->os->version; // ex. 6.1 (7601)
    echo $computer->os->service_pack; // ex. Service Pack 1

    echo $computer->type;

    echo $computer->group;

    echo $computer->host_name;

    print_r($computer->dn);
	
	
####Get a list of all computers
	
    $computers = Corp::computers();

    //Usage for laravel select
    Form::select('computers', $computers->lists('name', 'name'));

####Get a list of printers

    $printers = Corp::printers();

    //Usage for laravel select
    Form::select('printers', $printers->lists('name', 'name'));

####Authenticating with laravel's Auth driver but using LDAP

    if (Corp::auth($username, $password)) { //If Passes LDAP Auth

            if(!User::where('username', '=', $username)->first()){ // If web user profile does not exist, create one
                            $corpUser = Corp::user($username);

                            $user = new User;
                            $user->username = $username;
                            $user->email = $corpUser->email;
                            $user->fullname = $corpUser->name;
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
