define ['backbone', 'underscore', 'cs!mustache', 'cs!log', 'text!templates/item.mjs'], (Backbone, _, Mustache, log, ItemTemplate) ->
    log.debug(true)
    print = log.info
    warn = log.warn
    error = log.error

    tree = {}

    addItems = ($el, path) ->
        print "Adding elements from #{path}"

        parent = tree
        path.split('/').forEach (key) ->
            if key
                parent = parent[key]

        _.keys(parent).forEach (key, index) ->
            childrenNo = _.keys(parent[key]).length
            params =
                childrenNo: childrenNo
                isExpandable: childrenNo > 0
                name: key
                path: if path then path + "/" + key else key

            model = new ItemModel(params)
            view = new ItemView({model: model})

            $el.append(view.render().el)

    class TreeView extends Backbone.View
        tagName: 'ul'
        className: 'nav nav-list'

        initialize: (json) ->
            print "Initializing Tree View"
            tree = json or {}
            addItems.call(@, @$el, '')
            @$el.find("a").first().click()


    class ItemModel extends Backbone.Model
        defaults:
            path: ''
            name: ''
            isActive: false
            isExpandable: false
            isExpanded: false
            editable: false
            childrenNo: 1

    '''
        Render a new "ul" element
    '''
    class ItemView extends Backbone.View
        tagName: 'li'
        template: ItemTemplate
        $previousElement: ''

        events:
            "click a": "expandList"
            #"dblclick li": "editContent"
            #"keydown li": "saveContent"

        initialize: (options) ->
            #print "Initializing Item View"
            if options? and options.model?
                @model = options.model

        render: ->
            print "Rendering Item View"
            @$el.html(Mustache.to_html(@template, @model.toJSON()))
            @$el.find( "ul" ).last().sortable({connectWith: ".connectedSortable"}).disableSelection();;
            @

        expandList: (event)->
            event.stopPropagation()

            $.each $("li.active"), (k, v) ->
                $(v).removeClass("active")
            @$el.addClass('active')

            $el = @$el.find("ul").first()

            #if $el.is(@$previousElement)
            #   @$previousElement.attr('contenteditable', false);

            #if $el.find("a").attr('contenteditable') is "true"
            #   return @

            isExpandable = @model.get('isExpandable')

            if not isExpandable
                return @

            isExpanded = @model.get('isExpanded')
            @model.set('isExpanded', !isExpanded)
            @$el.find("i").toggleClass('icon-plus icon-minus')
            @model.set('isActive', true)


            if (!@hasBeenExpanded)
                @hasBeenExpanded = true
                addItems.call(@, $el, @model.get('path'))
                $el.hide()

            $el.slideToggle(200);

            $('.owl-tree').animate(
                 scrollTop: $el.offset().top - 50
                 300
            )

        editContent: (event) ->
            event.stopPropagation()
            $el = @.$el.find("li:first").find("a")
            $el.attr('contenteditable', true)
            $el.focus()
            @$previousElement = $el

        saveContent: (event)->
            event.stopPropagation()
            if event.keyCode is 13
                @.$el.find("li:first a").attr('contenteditable', false)

    return TreeView