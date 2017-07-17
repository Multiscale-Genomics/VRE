# author Alex Bardas
# data type for creating a Tree structure

define [], ->

    # Tree Node
    class Node
        constructor: (params) ->
            @children = []
            @parent = {}
            @name = if params? then params.name else ''

    # Tree's root, carries all the information needed 
    # to render a tree
    class Root extends Node
        constructor: (params) ->
            super params
            @parent = null

    Tree = {
        Node: Node
        Root: Root
    }