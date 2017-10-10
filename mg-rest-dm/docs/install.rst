Requirements and Installation
=============================

Requirements
------------

Software
^^^^^^^^
- Python 2.7.10+
- pyenv
- pyenv virtualenv
- pip

Python Modules
^^^^^^^^^^^^^^
- DMP
- Flask
- Flask-Restful
- pytest
- Waitress
- Sphinx
- sphinx-autobuild

Installation
------------

Basics
^^^^^^
Directly from GitHub:

.. code-block:: none
   :linenos:

   git clone https://github.com/Multiscale-Genomics/mg-rest-dm.git
   cd mg-rest-dm/
   pip install -e .
   pip install -r requirements.txt

Using pip:

.. code-block:: none
   :linenos:

   pip install git+https://github.com/Multiscale-Genomics/mg-rest-dm.git


Setting up a server
^^^^^^^^^^^^^^^^^^^
.. code-block:: none
   :linenos:

   git clone https://github.com/Multiscale-Genomics/mg-rest-dm.git

   cd mg-rest-dm
   pyenv virtualenv 2.7.12 mg-rest-dm
   pyenv activate mg-rest-dm
   pip install git+https://github.com/Multiscale-Genomics/mg-dm-api.git
   pip install -e .
   pyenv deactivate

Requires a file with the name `mongodb.cnf` with the following parameters to
define the MongoDB server:

.. code-block:: none
   :linenos:

   [dmp]
   host = localhost
   port = 27017
   user = testuser
   pass = test123
   db = dmp


Customise the `rest/auth_meta.json` file to locate the authentication server

Starting the service:

.. code-block:: none
   :linenos:

   nohup ${PATH_2_PYENV}/versions/2.7.12/envs/mg-rest-dm/bin/waitress-serve --listen=127.0.0.1:5002 rest.app:APP &

Testing
---------
Test scripts are located in the `test/` directory. Run `pytest` to from the root
repository directory to ensure that the API is working correctly.

The scripts require a valid hdf5 file generated using the scripts from
mg-storage-hdf5 and a matching datasets.json file located in the `rest/`
directory

Documentation
-------------
To build the documentation:

.. code-block:: none
   :linenos:

   pip install Sphinx
   pip install sphinx-autobuild
   cd docs
   make html
