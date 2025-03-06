<picture>
	<p align="center">
		<img src="https://github.com/REGmon-project/regmon/blob/main/img/regmon-logo-transparent.png" alt="REGMON logo" width="65%"/>
	</p>
</picture>
<hr>
<p align="center">
	Landing Page<br>
	https://regmon-project.org
</p>
<hr>
<p align="center">
	Preprint: "REGmon: A web application for athlete monitoring in sport practice and research" <br>
	https://sportrxiv.org/index.php/server/preprint/view/342
</p>
<hr>
<p align="center">
	REGMON Open Source Repository<br>
	https://github.com/REGmon-project/regmon
</p>
<hr>
<p align="center">
	REGMON Open Source Documentation<br>
	https://regmon-project.github.io/
</p>
<hr>

# Introduction
REGMON is a powerful web-based open source application designed to empower athletes, coaches and other sport practitioners to easily collect, analyze and visualize data. Individual athlete monitoring approaches covering the daily training process can be implemented by using customizable forms, dashboards, analysis templates and user-friendly graphical feedback. Furthermore, REGMON can also be used by researchers to enable efficient and GDPR compliant data management in various scenarios and projects.

<picture>
	<p align="center">
		<img src="https://github.com/REGmon-project/regmon/blob/main/img/regmon_frontend_collection.png" alt="REGMON front-end screenshot collection" width="95%"/>
	</p>
</picture>

# Features
- monitor and analyze training processes
- collect data through fully customizable forms
- create templates to easily analyze data and get individual insights
- visualize data using dashboards
- set specific permission settings between athletes, coaches and staff

# How to Install
There are two options for installing this project: using a Docker image (container installation) or installing it directly into your web server's document root (bare metal installation). Choose your installation method depending on your specific requirements and preferences. Based on your chosen installation method, ensure that you have met the necessary installation [requirements](#requirements). For more detailed information about the installation process, please checkout out our [installation guide](https://regmon-project.github.io/installation.html) guide.

## Requirements
We recommend opting for the container-based installation method, which only requires Docker Desktop to be installed on your web server. If you prefer to install on bare metal, please be mindful that discrepancies in the versions of installed software components may lead to potential issues.

### Container Installation (recommended)
* Docker Desktop

### Bare Metal Installation
* Apache (2.4) or nginx or any other php-ready web server
* PHP (8.2)
	* with extensions mbstring, zip, zlib, mysqli, pdo_mysql
* MySQL (5.7)
* Additional PHP and JS libraries
	* Composer for installation of additional PHP libraries
	* npm for installation of additional JS libraries

# How to Use
After installing REGMON, you can access the application via your web browser (e.g. Chrome, Safari, Firefox, etc.). If REGMON is set up on a web server, you may access the web application by typing the URL of your web server adding the "Application Domain name" you have set during the installation process. If you want to test REGMON locally, you can access the application after running the docker image by typing ``localhost:8000`` in your web browser.

Depending on the selected [configuration options](https://regmon-project.github.io/installation.html#configuration) there are several preconfigured profiles and sample data to test the app. For more detailed information about the usage of REGMON, please checkout out our [user guide](https://regmon-project.github.io/user_guide.html).

# License
REGMON is licensed under the [MIT License](https://opensource.org/licenses/MIT). You can find a copy of the license in the [LICENSE](LICENSE) file in the root of this repository.

# Acknowledgements
REGMON has been developed in and for the multicenter research program "REGman – Optimization of Training and Competition: Management of Regeneration in Elite Sports" ([OSF project page](https://osf.io/uz4af/)), which has been funded by the [Federal Institute of Sport Science in Germany](https://www.bisp.de). Furthermore, we acknowledge the contribution from CEOS solution GmbH (Bochum, Germany) throughout the project.

# Contributing (under construction)
<!-- If you consider contributing to REGMON, read the CONTRIBUTING.md (under construction). Please note that this package is released with a Contributor Code of Conduct (under construction). By contributing to this project, you agree to abide by its terms. -->
