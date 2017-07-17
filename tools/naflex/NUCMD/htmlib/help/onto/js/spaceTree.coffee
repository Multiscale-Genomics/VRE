define ['jit'], () ->

    Log =
        elem: false,
        write: (text) ->
            if (!this.elem)
                this.elem = document.getElementById('log')
            this.elem.innerHTML = text
            this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px'

    setRootNode = false

    $jit.ST.Plot.NodeTypes.implement(
        nodeline:
            render: (node, canvas, animating) ->
                if (animating == 'expand' || animating == 'contract')
                    pos = node.pos.getc(true)
                    nconfig = this.node
                    data = node.data

                    width  = nconfig.width
                    height = nconfig.height
                    algnPos = this.getAlignedPos(pos, width, height)
                    ctx = canvas.getCtx()
                    ort = this.config.orientation

                    ctx.beginPath();

                    if (ort == 'left' || ort == 'right')
                          ctx.moveTo(algnPos.x, algnPos.y + height / 2)
                          ctx.lineTo(algnPos.x + width, algnPos.y + height / 2)
                    else
                        ctx.moveTo(algnPos.x + width / 2, algnPos.y)
                        ctx.lineTo(algnPos.x + width / 2, algnPos.y + height)
                    ctx.stroke();
    )

    spaceTree = new $jit.ST (
        injectInto: 'owl-viz'
        duration: 400
        transition: $jit.Trans.Quart.easeInOut
        levelDistance: 60
        levelsToShow: 3
        Navigation:
            enable: true
            panning:true
        Node:
            height: 18
            width: 120
            type: 'none'
            color: '#333'
            overridable: false
        Edge:
            type: 'bezier'
            color:'#23A4FF'
            overridable: true

        onBeforeCompute: (node) ->
            Log.write("loading " + node.name)

        onAfterCompute: () ->
            Log.write("")

        onCreateLabel: (label, node) ->
            label.id = node.name;
            label.innerHTML = node.name;

            label.onclick = () ->
                #spaceTree.setRoot(node.id, 'animate');
                if node.getSubnodes().length > 1
                    spaceTree.onClick(node.id)
            #set label styles
            style = label.style
            style.width = 120 + 'px'
            style.height = 20 + 'px'
            style.cursor = 'pointer'
            style.color = '#333'
            style.fontWeight = 'bolder'
            style.fontSize = '1em'
            style.textAlign= 'center'
            style.paddingTop = '2px'

            if node.getSubnodes().length == 1
                style.color = "#aaa"


        onBeforePlotNode: (node) =>
            if (node.selected)
                node.data.$color = "#aaa"
            else
                delete node.data.$color

        onBeforePlotLine: (adj) ->
            if (adj.nodeFrom.selected && adj.nodeTo.selected)
                adj.data.$color = "#ff0000"
                adj.data.$lineWidth = 3
            else
                delete adj.data.$color
                delete adj.data.$lineWidth

        onComplete: ()  ->
            if (!setRootNode)
                m =
                    offsetX: 250
                spaceTree.onClick(spaceTree.root, { Move: m })
                setRootNode = true
    )