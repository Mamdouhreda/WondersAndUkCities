Wonders and UK Search Places
This project, crafted using Craft CMS, exhibits a breathtaking array of global wonders and includes a unique functionality to search for specific cities in the UK.

Overview
The webpage beautifully portrays various places around the globe through a slider. Each slide provides intricate details of the place, such as its name, address, and geolocation coordinates (latitude and longitude). This data is efficiently fetched from a dedicated Craft module named 'places'.

Moreover, the webpage includes a city search feature, which enables users to input the name of a city in the UK. The relevant details for the entered city are then fetched using a module named 'search'.

Dependencies
Craft CMS
Places Module
Search Module
Twig templating language
Tailwind CSS
Project Structure
The webpage is primarily split into two segments: the slider display section and the city search section. Each of these segments is created in a separate Twig block.

The slider section fetches data from the 'places' module and iterates over each place to generate a corresponding slide with the place's details.
The city search section incorporates a form that accepts a city name (specifically from the UK) as user input. The form then returns results that are relevant to the input city.
Getting Started
Follow the steps outlined below to get a local copy of the project up and running:

Clone the repo:

bash
Copy code
git clone https://github.com/Mamdouhreda/WondersAndUkCities
Install Craft CMS and the required dependencies:

Refer to the official guide for detailed instructions: Craft Installation

Start the server:

Copy code
php -S localhost:8000 -t web/

Usage
To use this project, navigate to the server address that your terminal displays after you run the server. You'll see a carousel showcasing wonders of the world, along with a search bar to search for specific cities in the UK.


Contact
Mamdouh Reda - mamdohreda@gmail.com

Project Link: https://github.com/Mamdouhreda/WondersAndUkCities