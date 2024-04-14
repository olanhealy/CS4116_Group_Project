### isAccountFound($email, $password):
- $email: The email address to check in the account table.
- $password: The password to check in the account table.

### setVerified($user_id, $verified):
- $user_id: The user ID whose verification status needs to be updated.
- $verified: The new verification status to set.

### getVerified($user_id):
- $user_id: The user ID to fetch the verification status for.

### setPassword($password, $user_id):
- $password: The new password to set for the user.
- $user_id: The user ID whose password needs to be updated.

### setFirstName($user_id, $first_name):
- $user_id: The user ID whose first name needs to be updated.
- $first_name: The new first name to set.

### setLastName($user_id, $last_Name):
- $user_id: The user ID whose last name needs to be updated.
- $last_Name: The new last name to set.

### setName($first_name, $last_name, $user_id):
- $first_name: The new first name to set.
- $last_name: The new last name to set.
- $user_id: The user ID whose name needs to be updated.

### setBio($user_id, $bio):
- $user_id: The user ID whose bio needs to be updated.
- $bio: The new bio to set.

### getBio($user_id):
- $user_id: The user ID to fetch the bio for.

### setGender($user_id, $gender):
- $user_id: The user ID whose gender needs to be updated.
- $gender: The new gender to set.

### getGender($user_id):
- $user_id: The user ID to fetch the gender for.

### setAge($age, $user_id):
- $age: The new age to set.
- $user_id: The user ID whose age needs to be updated.

### getAge($user_id):
- $user_id: The user ID to fetch the age for.

### setCollegeYear($user_id, $college_year):
- $user_id: The user ID whose college year needs to be updated.
- $college_year: The new college year to set.

### getCollegeYear($user_id):
- $user_id: The user ID to fetch the college year for.

### setPursuing($user_id, $pursuing):
- $user_id: The user ID whose pursuing status needs to be updated.
- $pursuing: The new pursuing status to set.

### getPursuing($user_id):
- $user_id: The user ID to fetch the pursuing status for.

### setProfilePic($user_id, $profile_pic_filename):
- $user_id: The user ID whose profile picture needs to be updated.
- $profile_pic_filename: The filename of the new profile picture.

### getProfilePicture($user_id):
- $user_id: The user ID to fetch the profile picture for.

### setCourse($user_id, $course):
- $user_id: The user ID whose course needs to be updated.
- $course: The new course to set.

### getCourse($user_id):
- $user_id: The user ID to fetch the course for.

### setHobbies($user_id, $hobbies):
- $user_id: The user ID whose hobbies need to be updated.
- $hobbies: The new hobbies to set.

### getHobbies($user_id):
- $user_id: The user ID to fetch the hobbies for.

### setLookingFor($user_id, $looking_for):
- $user_id: The user ID whose looking for status needs to be updated.
- $looking_for: The new looking for status to set.

### getLookingFor($user_id):
- $user_id: The user ID to fetch the looking for status for.

### getName($user_id):
- $user_id: The user ID to fetch the name for.

### getMatch($userId, $targetId):
- $userId: The ID of the user initiating the match.
- $targetId: The ID of the user being matched with.

### addMatch($initiatorId, $targetId):
- $initiatorId: The ID of the user initiating the match.
- $targetId: The ID of the user being matched with.

### isItAMatch($initiatorId, $targetId):
- $initiatorId: The ID of the user initiating the match.
- $targetId: The ID of the user being checked for a match.

### removeMatch($userId, $targetId):
- $userId: The ID of the user initiating the match.
- $targetId: The ID of the user being unmatched.

### getAllMatches($userId):
- $userId: The ID of the user to fetch all matches for.

### showProfileCard($user_id):
- $user_id: The user ID for which to generate a profile card.

### setupHeader():
- No arguments.

### setupFooter():
- No arguments.

### adoreUser($userLoggedInId, $currentUserId):
- $userLoggedInId: The ID of the user initiating the adore action.
- $currentUserId: The ID of the user being adored.

### ignoreUser($userLoggedInId, $currentUserId):
- $userLoggedInId: The ID of the user initiating the ignore action.
- $currentUserId: The ID of the user being ignored.

### isUserAdored($userId, $targetId):
- $userId: The ID of the user initiating the check.
- $targetId: The ID of the user being checked for adoration.
