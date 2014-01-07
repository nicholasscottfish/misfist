=== WP Project Managment Ultimate ===
Contributors: nohalfpixels
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P7CYJYR7623K6
Tags: project, management, productivity
Requires at least: 3.3
Tested up to: 3.3
Stable tag: 1.0.7

Simple to use project managment post type for designers / freelancers / anyone.

== Description ==

Simple to use project managment post type for designers / freelancers / anyone. 

Set project specifics add description and project details, then monitor the project through the comments section, with special front end image/file upload in comments.

Projects are fully protected, only viewable by users assigned to them.

Very simple, very easy. Use the built in comment system of WordPress to increase your productivity.

Features:

Add Project custom post type

Add Project Type Taxomony for Projects

Project Options:

* Title

* Project Description

* Project Type (taxomony)

* Start Date - jquery datepicker

* End Date - jquery datepicker

* Job Number

* Quote Number

* Invoice Number

* Payment Options **(No | Part + % | Yes)**

* Completed Percentage

* Project Users - WordPress Users

* Project Attachments - listed in metabox and can be deleted

		
Projects Filterable By:

* End Date

* Completed Amount

* Paid Amount

* Created Date

* Taxomony
		
Project Managment Options:
			
* Notifcation From Name (Email From Name)

* Notification From Email (customname@yourdomain.com)

* Custom Titles For Fields On Included Page Template

* Allow/Disallow Comment Uploads

* Custom CSS for included Page Template
		
Included Page Template W/T Simple Styling/Layout To Display Projects *(can be over-written by adding a `single-projects.php` to the current theme)*

Notification System Which Emails Project Users When Project Updated / Or Commented

Adds Upload Field To The Projects Comments Section Allowing New Files To Be Uploaded *(And Wp Allowed File Types)*, Which Get Added As Attachments To Post And Included In The Comment

A Support page which can send your wp details (server info, plugins active, etc) with your custom message to make support much easier.

*Credits have to go to Fat Cow Hosting for use of there great folder icon (released under Creative Commons Attribution 3.0 License | http://www.fatcow.com/free-icons)*


== Installation ==

1. Download and Unzip the `wp-project-managment-ultimate.zip` file
2. Upload `wp-project-managment-ultimate` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why is it when i create a project and i try to view it i get a 404 error? =

This means your rewrite rules haven't yet been updated (associated with permalinks). All you need to do is navigate to **Settings->Permalinks** and just hit the save changes button.

== Screenshots ==

1. The Projects List Screen - Contains additional fields in the post table to quickly see and filter projects. Projects sortable by end date, amount completed, or amount paid. Job numbers, Quote numbers, Invoice numbers (if used) can also bee seen here at a quick glance.
2. The Project Edit Screen - Contains Project Title, Description, Project Type (taxomony), Project Attachments (editable | deletable), Project Details (including: "start date", "end date", "job number", "quote number", "invoice number", "paid amount", "completed amount"), and project users list. All editable.
3. Project Start Date / End Date Meta Box - Uses jQuery datepicker for easier date picking.
4. Project Type (taxomony) - Project Taxomony used to seperate projects by type (example types could include: "web", "print", "video", "animation", "app development", "etc").
5. The Settings Page - This is where you can configure the settings for the notifications, allow/disallow the comment uploads feature, and change text/css for the included page template.
6. The Page Template - The included page template is very simply styled to cater for as many themes as possible. All the css is well selected and targeting elements is very easy. Or you can just copy the content of the template files to a new template within your theme and customize to your hearts content. 

== Changelog ==

=1.0.7=

* Bug Fixes

=1.0.6=

* Updated forum link

=1.0.5=

* Added WP widget to list a users posts
* Added WP short code to sliest users projects [wp-project-managment]

=1.0.4=

* fixed missing link in email notification

=1.0.3=

* better support for sub domains when setting from email address
* fixed 404 errors for projects on plugin activation
* fixed FAQ page link in help page
* better contextual help - removed help/support page replaced with contextual help screen objects
* minor bug fixes

=1.0.2=

* Added help tabs to project edit screen
* Added help tabs to project view screen
* Fixed typo on help / support page

=1.0.1=

* Added POT file for translations
* Added "productivity" tag for repository
* Added permalinks 404 error to the FAQ section
* Added FAQ Link step on the plugins support page
* Changed step 1 in the install instructions to better explain the process
* Changed folder name in step 2 of the install instructions
* Reformatted the readme description for better visibility in the plugins directory
* Updated Plugin URI to reflect the plugins project page on http://no-half-pixels.com

= 1.0 =
* No Changes Yet