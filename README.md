# Trudge #

A PHP based backdoor terminal &amp; explorer for penetration testing.  With such a tool WordPress administrator accounts can be made, Metasploit backdoors can be set up, and much more.

## Goals ##

1. Be a lightweight script capable of editing, browsing, creating, and deleting files.
2. Allow the execution of shell commands in a simplistic but powerful way.
3. Avoid detection and avoid leaving evidence of activities.
4. Quickly test small pre-built scripts for quick penetration testing.
5. Use a qualified editor for making changes to files.
6. Be secured to prevent crawlers/others from discovering the backdoor and potentially using it.

## Features ##

### Hidden from Bots ###

For now a meta tag is always in the header telling search engine crawlers to not index the page.  If Trudge pops up in Google results for somebody's website, that could potentially give away the presence of trudge.  This is also to prevent messing up SEO results for websites _(per "Goals - 3")._

```
<meta name="robots" content="noindex">
```

### Terminal Access ###

Currently *index.php* is a terminal that allows you to send commands to the server system.  *index.php* makes a post request to *x.php* and *x.php* then executes the command via ```shell_exec```.

### Directory Explorer ###

The servers file structure can easily be navigated utilizing *browse.php*.  When a directory is clicked on the active directory changes to that directory and when a file is clicked on *edit.php* opens up to edit that file.

### Adding Files / Directories ###

New files and directories can be made with the Directory Explorer (*browse.php*).

### Editing Existing Files ###

Files can be edited in *edit.php* and then saved.  The raw data can also be displayed in a new tab if you would like to download the code.

## What Needs Fixing / TODO ##

* Trudge should ideally be one PHP file and not have to rely on so many other files.
* Trudge should be minified (thus helping to avoid file size limits).
* Trudge should have some form of authentication to prevent 3rd party attackers.
* Trudge should not make post / get requests that contain raw information that the server's logs can pick up on.
* Trudge should be able to rename files and directories.
* Trudge should be able to delete files and directories.
* Trudge should be able to compress directories / multiple files for easier download.
* Trudge should be able to relay information to an external server.