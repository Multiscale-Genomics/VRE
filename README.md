# VRE 
MuG Virtual Research Environment.
MuG VRE is a web application aimed to allow MuG users to access MuG data and explore and exploit it together with its own data via a selection of tools and visualizers.
It is written in PHP, HTML and Javascript.

## URL
 [https://vre.multiscalegenomics.eu/](https://vre.multiscalegenomics.eu) : production web site - version 1.1
* [https://dev.multiscalegenomics.eu/](https://dev.multiscalegenomics.eu) : development web site - version 1.2

## Code structure
 * [admin](./admin) code related to admin users, for managing tools, users, resources, etc
 * [applib](./applib) library for code invoked via AJAX
 * [assets](./assets) layout code
 * [htmlib](./htmlib) basic HTML templates
 * [phplib](./phplib) PHP libraries for managing Mongo db, SGE jobs, users, etc.
 * [errors](./errors) HTML for error pages
 * HTML related to each of the VRE seccions:
    * [getdata](./getdata) upload files to VRE
    * [help](./help) help and documentation section
    * [helpdesk](./helpdesk) ticketing system submission
    * [home](./home) VRE home
    * [repository](./repository) import data from VRE repositories
    * [user](./user) user profile registry and modification 
    * [workspace](./workspace) user files management
 * [tools](./tools) VRE tools. input forms and results preview
 * [visualizer](./visualizer) VRE visualizers
