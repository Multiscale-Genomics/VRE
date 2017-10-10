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

import os
import tempfile
import json
import bson
from bson.objectid import ObjectId
import pytest

from context import app

@pytest.fixture
def client(request):
    """
    Definges the client object to make requests against
    """
    db_fd, app.APP.config['DATABASE'] = tempfile.mkstemp()
    app.APP.config['TESTING'] = True
    client = app.APP.test_client()

    def teardown():
        """
        Close the client once testing has completed
        """
        os.close(db_fd)
        os.unlink(app.APP.config['DATABASE'])
    request.addfinalizer(teardown)

    return client

def test_file_01(client):
    """
    Test that the track endpoint is returning the usage paramerts
    """
    rest_value = client.get(
        '/mug/api/dmp/file_meta',
        headers=dict(Authorization='Authorization: Bearer teststring')
    )
    details = json.loads(rest_value.data)
    # print(details)
    assert 'usage' in details

def test_file_02(client):
    """
    Test that the track endpoint is returning data when a user_id is specified
    and that there are no locations
    """

    file_ids = [
        'testtest0000', 'testtest0001', 'testtest0002', 'testtest0003'
    ]

    for file_id in file_ids:
        print("\n", file_id, "\n ============")
        rest_value = client.get(
            '/mug/api/dmp/file_meta?file_id=' + str(file_id),
            headers=dict(Authorization='Authorization: Bearer teststring')
        )

        details = json.loads(rest_value.data)
        print(rest_value.status_code, details["_id"])
        print("ObjectId:", str(ObjectId(str(file_id))))
        assert str(details["_id"]) == str(ObjectId(str(file_id)))
