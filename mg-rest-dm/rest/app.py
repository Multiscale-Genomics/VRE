"""
.. See the NOTICE file distributed with this work for additional information
   regarding copyright ownership.

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
"""

from __future__ import print_function

import json
import os
import sys
#import logging

from flask import Flask, request
from flask_restful import Api, Resource

from dmp import dmp
from reader.hdf5_reader import hdf5_reader

from mg_rest_util.mg_auth import authorized

APP = Flask(__name__)
#logging.basicConfig()

def help_usage(error_message, status_code,
               parameters_required, parameters_provided):
    """
    Usage Help

    Description of the basic usage patterns for GET functions for the app,
    including any parameters that were provided byt he user along with the
    available parameters that are required/optional.

    Parameters
    ----------
    error_message : str | None
        Error message detailing what has gone wrong. If there are no errors then
        None should be passed.
    status_code : int
        HTTP status code.
    parameters_required : list
        List of the text names for each paramter required by the end point. An
        empty list should be provided if there are no parameters required
    parameters_provided : dict
        Dictionary of the parameters and the matching values provided by the
        user. An empyt dictionary should be passed if there were no parameters
        provided by the user.

    Returns
    -------
    str
        JSON formated status message to display to the user
    """
    parameters = {
        'by_user': ['By User [0|1]', 'int', 'OPTIONAL'],
        'file_id': ['File ID', 'str', 'REQUIRED'],
        'region': ['Chromosome:Start:End', 'str:int:int', 'OPTIONAL'],
        'file_type': ['File type (bb, bw, tsv, fasta, fastq, ...)', 'str', 'OPTIONAL'],
        'data_type': ['Data type (chip-seq, rna-seq, wgbs, ...)', 'str', 'OPTIONAL'],
        'assembly': ['Assembly', 'str', 'REQUIRED'],
        'chrom': ['Chromosome', 'str', 'OPTIONAL'],
        'start': ['Start', 'int', 'OPTIONAL'],
        'end': ['End', 'int', 'OPTIONAL'],
        'type': ['add_meta|remove_meta', 'str', 'OPTIONAL'],
        'output': [
            "Default is None. State 'original' to return the original whole file",
            'str', 'OPTIONAL'],
    }

    used_param = {k : parameters[k] for k in parameters_required if k in parameters}

    usage = {
        '_links' : {
            '_self' : request.base_url,
            '_parent' : request.url_root + 'mug/api/dmp'
        },
        'parameters' : used_param
    }
    message = {
        'usage' : usage,
        'status_code' : status_code
    }

    if parameters_provided:
        message['provided_parameters'] = parameters_provided

    if error_message is not None:
        message['error'] = error_message

    return message

def _get_dm_api(user_id=None):
    cnf_loc = os.path.dirname(os.path.abspath(__file__)) + '/mongodb.cnf'

    if user_id == 'test':
        print("TEST USER DM API")
        return dmp(cnf_loc, test=True)

    if os.path.isfile(cnf_loc) is True:
        print("LIVE DM API")
        return dmp(cnf_loc)

    print("TEST DM API")
    return dmp(cnf_loc, test=True)

class EndPoints(Resource):
    """
    Class to handle the http requests for returning information about the end
    points
    """

    def get(self):
        """
        GET list all end points

        List of all of the end points for the current service.

        Example
        -------
        .. code-block:: none
           :linenos:

           curl -X GET http://localhost:5002/mug/api/dmp

        """
        return {
            '_links': {
                '_self': request.base_url,
                '_getFile': request.url_root + 'mug/api/dmp/file',
                '_getFiles': request.url_root + 'mug/api/dmp/files',
                '_getFileHistory': request.url_root + 'mug/api/dmp/file_history',
                '_ping': request.url_root + 'mug/api/dmp/ping',
                '_parent': request.url_root + 'mug/api'
            }
        }


class FileMeta(Resource):
    """
    Class to handle the http requests for retrieving the data from a file.
    This class is able to handle big[Bed|Wig] file and serve back the matching
    region in the relevant format. It is also possible to stream back the whole
    file of any type for use in other tools.
    """

    @authorized
    def get(self, user_id):
        """
        GET  List values from the file

        Call to optain regions from the conpressed index files for Bed, Wig and
        TSV based file formats that contain genomic information.

        Other files can be streamed.

        Parameters
        ----------
        user_id : str
            User ID
        file_id : str
            Identifier of the file to retrieve data from

        Returns
        -------
        file
            Returns a formated in the relevant file type with any genomic
            features matching the format of the file.

        Examples
        --------
        .. code-block:: none
           :linenos:

           curl -X GET http://localhost:5002/mug/api/dmp/track?file_id=test_file

        """
        file_id = request.args.get('file_id')

        params = [file_id]

        # Display the parameters available
        if sum([x is None for x in params]) == len(params):
            return help_usage(None, 200, ['file_id'], {})

        # ERROR - one of the required parameters is NoneType
        if sum([x is not None for x in params]) != len(params):
            return help_usage('MissingParameters', 400, ['file_id'], {})

        if user_id is not None:
            dmp_api = _get_dm_api(user_id['user_id'])
            return dmp_api.get_file_by_id(user_id['user_id'], file_id, True)

        return help_usage('Forbidden', 403, ['file_id'], {})

    @authorized
    def post(self, user_id):
        """
        POST Add a new file to the DM API

        Parameters
        ----------
        This should be passed as the data block with the HTTP request:

        json : dict
            user_id : str
                User identifier
            file_path : str
                Location of the file
            file_type : str
                Tag for the file extension. The valid parameters are defined
                within the DM API documentation (mg-dm-api)
            data_type : str
                What type of experiment is this data from. Options include
                    RNA-seq
                    ChIP-seq
                    MNase-seq
                    WGBS
                    HiC
            taxon_id : int
                Taxonomic identifier for a species (Human = 9606)
            compressed
                Options of the compression level of the file. If file is not
                compressed then do not include this parameter
            source_id : list
                List of file_ids that were used for generating this file.
                If this is the root file then do not include this parameter.
            meta_data : dict
                Hash array describing the relevant metadata for the file,
                including the assembly if relevant

        Returns
        -------
        file_id
            Returns the id of the stored file

        Example
        -------
        .. code-block:: none
           :linenos:

           echo '{
               "data_type": "RNA-seq",
               "file_type": "fastq",
               "meta_data": {
                   "assembly" : "GRCh38"
               }, "taxon_id": 9606,
               "file_path": "/tmp/test/path/RNA-seq/testing_123.fastq",
               "parent_dir" "/tmp/test/path/RNA-seq/",
               "size": 64000,
           }' > data.json

           curl -X POST
               -H "Content-Type: application/json"   \
               -H "Authorization: Bearer teststring" \
               -d @data.json http://localhost:5002/mug/api/dmp/file

        """
        if user_id is not None:
            dmp_api = _get_dm_api(user_id['user_id'])

            new_track = json.loads(request.data)
            file_path = new_track['file_path'] if 'file_path' in new_track else None
            file_type = new_track['file_type'] if 'file_type' in new_track else None
            size = new_track['size'] if 'size' in new_track else None
            parent_dir = new_track['parent_dir'] if 'parent_dir' in new_track else None
            data_type = new_track['data_type'] if 'data_type' in new_track else None
            taxon_id = new_track['taxon_id'] if 'taxon_id' in new_track else None
            source_id = new_track['source_id'] if 'source_id' in new_track else None
            meta_data = new_track['meta_data'] if 'meta_data' in new_track else None
            compressed = new_track['compressed'] if 'compressed' in new_track else None

            params_required = ['user_id', 'file_path', 'file_type', 'data_type',
                               'taxon_id', 'source_id', 'meta_data']
            params = [user_id, file_path, file_type, data_type, taxon_id,
                      source_id, meta_data]

            # ERROR - one of the required parameters is NoneType
            if sum([x is not None for x in params]) != len(params):
                return help_usage('MissingParameters', 400, params_required,
                                  user_id)

            new_track = json.loads(request.data)

            return dmp_api.set_file(
                user_id['user_id'],
                file_path,
                file_type,
                size,
                parent_dir,
                data_type,
                taxon_id,
                compressed,
                source_id,
                meta_data
            )

        return help_usage('Forbidden', 403, [], {})

    @authorized
    def put(self, user_id):
        """
        PUT Update meta data

        Request to update the meta data for a given file. This allows for the
        adding or removal of key-value pairs from the meta data.

        Parameters
        ----------
        This should be passed as the data block with the HTTP request:

        json : dict
            user_id : str
                User identifier
            file_id : str
                ID of the stored file
            type : str
                Options are 'add_meta' or 'remove_meta' to modify they key-value
                pairs for the file entry. Minimum sets of pairs are defined
                within the DM API (mg-dm-api)
            meta_data : dict
                Hash array describing the relevant metadata key-value pairs that
                are to be added

        Returns
        -------
        file_id
            Returns the id of the stored file

        Example
        -------
        To add a new key value pair:

        .. code-block:: none
           :linenos:

           echo '{
               "type":"add_meta",
               "file_id":"<file_id>",
               "meta_data":{"citation":"PMID:1234567890"}
           }' > data.json

           curl -X PUT
               -H "Content-Type: application/json"   \
               -H "Authorization: Bearer teststring" \
               -d @data.json http://localhost:5002/mug/api/dmp/file

        To remove a key value pair:

        .. code-block:: none
           :linenos:

           echo '{
               "type":"remove_meta",
               "file_id":"<file_id>",
               "meta_data":["citation"]
           }' > data.json

           curl -X PUT
               -H "Content-Type: application/json"   \
               -H "Authorization: Bearer teststring" \
               -d @data.json http://localhost:5002/mug/api/dmp/file

        To modify a column value (file size):

        .. code-block:: none
           :linenos:

           echo '{
               "type":"modify_column",
               "file_id":"<file_id>",
               "key":"<column_key>"
               "value":"<new_value>"
           }' > data.json

           curl -X PUT
               -H "Content-Type: application/json"   \
               -H "Authorization: Bearer teststring" \
               -d @data.json http://localhost:5002/mug/api/dmp/file

        """
        if user_id is not None:
            dmp_api = _get_dm_api(user_id['user_id'])

            data_put = json.loads(request.data)
            file_id = data_put['file_id']

            params_required = ['user_id', 'file_id', 'type']

            if data_put['type'] == 'add_meta':
                for k in data_put['meta_data']:
                    result = dmp_api.add_file_metadata(
                        user_id['user_id'], file_id, k, data_put['meta_data'][k])
            elif data_put['type'] == 'remove_meta':
                for k in data_put['meta_data']:
                    result = dmp_api.remove_file_metadata(user_id['user_id'], file_id, k)
            elif data_put['type'] == 'modify_column':
                result = dmp_api.modify_column(
                    user_id['user_id'], file_id, data_put['key'], data_put['value']
                )
            else:
                return help_usage('MissingMetaDataParameters', 400, params_required,
                                  {'type' : ['add_meta', 'remove_meta', 'modify_column']})
            return result

        return help_usage('Forbidden', 403, [], {})

    @authorized
    def delete(self, user_id):
        """
        DELETE Remove a file from the DM API

        Function to remove the file from teh DM API. This will result in the
        file being removed from the records and therefore not available within
        the VRE or from the RESTful interface

        Parameters
        ----------
        This should be passed as the data block with the HTTP request:
        json : dict
            user_id : str
                User identifier
            file_id : str
                ID of the stored file

        Example
        -------
        .. code-block:: none
           :linenos:

           echo '{
               "file_id":"<file_id>"
           }' > data.json

           curl -X DELETE
               -H "Content-Type: application/json"   \
               -H "Authorization: Bearer teststring" \
               -d @data.json http://localhost:5002/mug/api/dmp/file

        """
        if user_id is not None:
            dmp_api = _get_dm_api(user_id['user_id'])

            params_required = ['user_id', 'file_id']
            data_delete = json.loads(request.data)
            if data_delete['file_id']:
                file_id = dmp_api.remove_file(user_id['user_id'], data_delete['file_id'])
            else:
                return help_usage('MissingMetaDataParameters', 400, params_required,
                                  {})
            return file_id

        return help_usage('Forbidden', 403, [], {})

class Files(Resource):
    """
    Class to handle the http requests for retrieving the list of files for a
    given user handle
    """

    @authorized
    def get(self, user_id):
        """
        GET List user tracks

        Function to list the filess that are owned by a single user. It is
        possible to filter by assembly, file or data type, or to find track
        files that contain data for a given region

        Parameters
        ----------
        assembly : str
            Genome assembly accession
        region : str
            <chromosome>:<start_pos>:<end_pos>
        file_type : str
        data_type : str

        Example
        -------
        .. code-block:: none
           :linenos:

           curl -X GET http://localhost:5002/mug/api/dmp/Files?>

        """
        if user_id is not None:
            region = request.args.get('region')
            assembly = request.args.get('assembly')
            file_type = request.args.get('file_type')
            data_type = request.args.get('data_type')
            by_user = request.args.get('by_user')

            params = [user_id]

            # Display the parameters available
            if sum([x is None for x in params]) == len(params):
                return help_usage(
                    None, 200,
                    ['region', 'assembly', 'file_type', 'data_type', 'by_user'], {})

            dmp_api = _get_dm_api(user_id['user_id'])

            files = []
            if region is not None and assembly is not None:
                files = self._get_all_files_region(dmp_api, user_id['user_id'], assembly, region)
            elif file_type is not None and assembly is not None:
                files = dmp_api.get_files_by_file_type(user_id['user_id'], rest=True)
            elif data_type is not None and assembly is not None:
                files = dmp_api.get_files_by_data_type(user_id['user_id'], rest=True)
            elif assembly is not None:
                files = dmp_api.get_files_by_assembly(user_id['user_id'], assembly, rest=True)
            elif by_user is not None and int(by_user) == 1:
                files = dmp_api.get_files_by_user(user_id['user_id'], rest=True)
            else:
                return help_usage(
                    None, 200,
                    ['region', 'assembly', 'file_type', 'data_type', 'by_user'], {})

            return {
                '_links': {
                    '_self': request.base_url,
                    '_parent' : request.url_root + 'mug/api/dmp'
                },
                'files': files
            }

        return help_usage(
            None, 200,
            ['region', 'assembly', 'file_type', 'data_type', 'by_user'], {})

    def _get_all_files_region(self, dmp_api, user_id, assembly, region):
        files = []
        chrom, start, end = region.split(':')
        h5_idx = hdf5_reader(user_id['user_id'])
        potential_files = h5_idx.get_regions(assembly, chrom, int(start), int(end))
        for f_in in potential_files[1]:
            files.append(dmp_api.get_file_by_id(f_in))
        for f_in in potential_files[1000]:
            files.append(dmp_api.get_file_by_id(f_in))
        return files


class FileHistory(Resource):
    """
    Class to handle the http requests for retrieving the list of file history of
    a given file for a given user handle
    """

    @authorized
    def get(self, user_id):
        """
        GET the list of files that were used for generating the defined file

        Example
        -------
        .. code-block:: none
           :linenos:

           curl -X GET http://localhost:5002/mug/api/dmp/file_history?file_id=<file_id>
        """
        if user_id is not None:
            dmp_api = _get_dm_api(user_id['user_id'])

            file_id = request.args.get('file_id')

            params = [user_id, file_id]

            # Display the parameters available
            if sum([x is None for x in params]) == len(params):
                return help_usage(None, 200, [], {})

            # ERROR - one of the required parameters is NoneType
            if sum([x is not None for x in params]) != len(params):
                return help_usage('MissingParameters', 400, [],
                                  {
                                      'file_id' : file_id
                                  })

            files = dmp_api.get_file_history(user_id['user_id'], file_id)

            return {
                '_links': {
                    '_self': request.base_url,
                    '_parent' : request.url_root + 'mug/api/dmp'
                },
                'history_files': files
            }

        return help_usage('Forbidden', 403, [], {})

class Ping(Resource):
    """
    Class to handle the http requests to ping a service
    """

    def get(self):
        """
        GET Status

        List the current status of the service along with the relevant
        information about the version.

        Example
        -------
        .. code-block:: none
           :linenos:

           curl -X GET http://localhost:5002/mug/api/dmp/ping

        """
        import rest.release as release
        res = {
            "status":  "ready",
            "version": release.__version__,
            "author":  release.__author__,
            "license": release.__license__,
            "name":    release.__rest_name__,
            "description": release.__description__,
            "_links" : {
                '_self' : request.base_url,
                '_parent' : request.url_root + 'mug/api/dmp'
            }
        }
        return res

#
# For the services where there needs to be an extra layer (adjacency lists),
# then there needs to be a way of forwarding for this. But the majority of
# things can be redirected to the raw files for use as a track.
#

sys._auth_meta_json = os.path.dirname(os.path.realpath(__file__)) + '/auth_meta.json'

# Define the URIs and their matching methods
REST_API = Api(APP)

#   List the available end points for this service
REST_API.add_resource(EndPoints, "/mug/api/dmp", endpoint='dmp_root')

#   Get the data for a specific track
REST_API.add_resource(FileMeta, "/mug/api/dmp/file_meta", endpoint='file_meta')

#   List the available species for which there are datasets available
REST_API.add_resource(Files, "/mug/api/dmp/files", endpoint='files')

#   List file history
REST_API.add_resource(FileHistory, "/mug/api/dmp/file_history", endpoint='file_history')

#   Service ping
REST_API.add_resource(Ping, "/mug/api/dmp/ping", endpoint='dmp-ping')


# Initialise the server
if __name__ == "__main__":
    APP.run(port=5002, debug=True, use_reloader=False)
