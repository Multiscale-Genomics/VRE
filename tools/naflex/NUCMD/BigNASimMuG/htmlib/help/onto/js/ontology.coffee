# author Alex Bardas
# Converts a valid XML ontology generated in *Protege* into a JSON
# First it creates the ontology's tree defined by root and children nodes
# Then, it creates a proper json from this tree

define ['cs!log', 'cs!tree'], (log, Tree) ->
    # set debug to true or false
    log.debug(false)
    print = log.info
    warn = log.warn
    error = log.error

    Node = Tree.Node
    Root = Tree.Root

    class Ontology

        type = Object.prototype.toString

        constructor: (owl) ->
            print "Initialize Ontology"
            # receives a string and checks if it is
            # a valid XML so it can return a proper JSON
            # after parsing it
            if not (xml = @_isValidXML?(owl))
                return {
                    status: "Error"
                    message: "The parameter is not a valid XML"
                }
            else
                root = @_toTreeObject(xml)

            if not type.call(root) == "[object Object]"
                return false

            return [@toSimpleJSON(root), @toJSON(root)]

        _isValidXML: (owl) ->
            print "Test if XML is valid"
            # tries to see if ontology is a valid XML document
            # return false if the document is not a XML
            # else returns the document

            try
                owl = $.parseXML(owl)
            catch err
                error "The ontology is not a valid XML", err
                return false

            return owl

        _toTreeObject: (xml) ->
            ###
                Receives a valid xml, parses it and receives a valid tree
            ###
            print "Create Tree"
            tree = {}
            root = 0

            $.each $(xml).find("Declaration"), (k, v) ->
                v = $(v).children().eq(0)
                name = v.attr("IRI")

                if name?
                    # remove first character if it is "#"
                    name = name.replace(/^#/, '')
                    tree[name] = new Node({name: name})
                else
                    root_name = v.attr("abbreviatedIRI")
                    # remove first characters until ":"
                    # e.g.: owl:Thing -> Thing
                    # e.g.: owl:owl:Thing -> owl:Thing
                    root_name = root_name.replace(/.*?:/, '')
                    if root_name
                        tree[root_name] = new Root({name: root_name})
                        root = tree[root_name]

                true

            $.each $(xml).find("SubClassOf"), (k, v) ->
                v = $(v).children()
                name = v.eq(0).attr("IRI").replace(/^#/, '')
                parent_name = v.eq(1).attr("IRI")
                parent_name = if parent_name then parent_name.replace(/^#/, '') else v.eq(1).attr("abbreviatedIRI").replace(/.*?:/, '')
                tree[name].parent = tree[parent_name]
                tree[parent_name].children.push(tree[name])
                true

            # the root has information about the whole tree
            root

        toSimpleJSON: (root) ->
            print "Create Simple JSON"
            ###
                Transforms the tree obtained from parsing the xml into an expressive object
                The tree will be an object similar:
                    root: {
                        child1: {
                            child1_1: {}
                        }
                        child2: {}
                    }
                Can have any depth, the name of the Node is actually the key name
                If the key is a leaf, it's value would be an empty object
                Else it's value would be an object containing its children

                Probably the easiest way to represent a Tree as a javascript object
            ###
            json = {}
            json[root.name] = {}

            traversal = (node, json) ->
                # check if it is a leaf
                # console.log node
                if (node.children?)
                 if node.children.length is 0
                     {}
                 else
                     for child in node.children
                         json[node.name][child.name] = {}
                         traversal(child, json[node.name])

            traversal(root, json)

            return json

        toJSON: (root) ->
            ###
                Transforms the tree obtained from parsing the xml into an expressive object
                The tree will be an object similar:
                {
                    name: "root"
                    children: [
                        {
                            name: "child1"
                            children: [
                                {
                                    name: "child1_1"
                                    children: []
                                }
                            ]
                        }, {
                            name: "child2"
                            children: []
                        }
                    ]
                }

                Can have any depth
                It's harder to manually create a JSON like this (than the JSON
                returned by Simple JSON function) but can be more useful because it
                can store more data
            ###
            print "Create JSON"

            json =
                name: root.name
                id: root.name
                children: []

            traversal = (node, json) ->
                # check if it is a leaf
                if (node.children?) 
	                if node.children.length is 0
        	             return {
                	         name: node.name
                        	 id: node.name
	                         children: []
        	             }
                	 else
	                    node.children.forEach( (child, index) ->
        	                json.push(
                	            name: child.name
                        	    id: child.name
	                            children: []
        	                )
                	        traversal(child, json[index].children)
	                    )

            traversal(root, json.children)

            return json
