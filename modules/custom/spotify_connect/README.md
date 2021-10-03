## Spotify Connect

### Instructions

1. Generate a `client_id` & `client_secret` in spotify's developer portal [authorisation guide](https://developer.spotify.com/documentation/general/guides/authorization-guide/)
2. Install the Drupal module as a custom module
3. Go to `admin/config/development/spotify` and enter your Spotify app's `client_secret` and `client_id` along with the accounts uri and base uri
4. Visit `admin/structure/block` to place an instance of the `Spotify artists` block into a region
   1. You can add a title to your block, along with a list of artists to display
5. The search uses Spotify's api that brings back a list of artists.
6. When you view the homepage you should see a list of artists, you can click their links to view a basic page about the artist.

### PHPSpecs

To run the phpspec tests run the following command in the root of the project
````
vendor/bin/phpspec run --verbose
````

### Behat

To run the behat tests run the following command in the root of the project
````
vendor/bin/behat
````

### Assumptions

1. There was no option in Spotify's api documentation to retrieve a list of x amount of artists, I therefore created the form for the admin user to add upto 20 artists.
2. This BE focused task, I haven't spent much time on FE work.
3. The access token runs out, therefore there is functionality built in to refresh the token with the client secret and id

### Possible Improvements
1. Encrypt the client secret
2. Improve styling
3. Add a warning to the admin user of missing client credentials in the block configure page
