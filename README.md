# Trudge #

A PHP based backdoor terminal &amp; explorer for penetration testing and for use as a utility.

## Goals ##

1. Be a lightweight script capable of editing, browsing, creating, and deleting files.
2. Allow the execution of shell commands from a web page.
3. Avoid detection and avoid leaving evidence of activities where possible.
4. Be small enough to upload in most conditions.
5. Provide a poor man's CMS for those lacking access to FTP or another CMS.
6. Be secured to prevent crawlers/others from discovering the page and potentially using it.
7. Provide all features without needing more than one file.


## Features ##

### Hidden from Bots ###

For now a meta tag is always in the header telling search engine crawlers to not index the page.  If Trudge pops up in Google results for somebody's website, that could potentially give away the presence of Trudge.  This is also to prevent messing up SEO results for websites _(per "Goals - 3")._

```
<meta name="robots" content="noindex">
```

### Terminal Access ###

Trudge can make *POST* requests to itself that, using ```shell_exec```, will run shell commands and return the output to the console.  All commands are run as the user running the web server process - normally Apache user.  This gives fairly open access to the current site and can sometimes allow access to other sites hosted on the server.

### Directory Explorer ###

The servers file structure can easily be navigated the built-in *Directory Explorer*.  When a directory is clicked on the active directory changes to that directory and when a file is clicked on it is displayed in the preview window for viewing, editing, or downloading.  You can select files and folders by clicking on the *empty space* around their entry in the *Directory Explorer*.  This allows you to run commands on multiple files at once (e.g. deleting several files).

### Adding Files / Directories ###

New files and directories can be created using the commands located in the *Tools* window (top-right).

### Editing Existing Files ###

Files that are recognized as an editable file (e.g. text files, css, php, etc.) are editable in the *File* window (bottom-left).  After making your changes, just press "Save File" below the text area.

### Sending Emails ###

~~Emails can be sent from any user that the server has permission to send emails as using *email.php*.~~

*Functionality for sending emails was removed in version 0.13.  It will likely return.*

## What Needs Fixing / TODO ##

* ~~Trudge should ideally be one PHP file and not have to rely on so many other files.~~ *Fixed in v0.13*
* ~~Trudge should have some form of authentication to prevent 3rd party attackers.~~ *Fixed in v0.13*
* ~~Trudge should be able to rename files and directories.~~ *Fixed in v0.13*
* ~~Trudge should be able to delete files and directories.~~ *Fixed in v0.13*
* ~~Trudge should be able to compress directories / multiple files for easier download.~~ *Fixed in v0.13 - Decompression has been added as well*
* Trudge should not make post / get requests that contain raw information that the server's logs can pick up on.  PHP session?
* Trudge should be minified (thus helping to avoid file size limits).
* Trudge needs to have some corner-cases cleaned up (e.g. selecting multiple files and then choosing compress doesn't work as expected).
* Trudge needs to be made smaller (current goal: 10 KB).