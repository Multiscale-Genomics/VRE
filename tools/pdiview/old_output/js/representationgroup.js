/*************************
 * Representation groups API
 *
 * TODO: Use NGL.RepresentationCollection
 */
var RepresentationGroup = function(component, name, selection, representations, 
                               defaultParameters) {
    
    /* Representation Group
     * 
     * Define a group of representations. The global visibility of this group
     * is controlled by the "visible" property.
     *
     * Arguments
     * ---------
     *
     * component	 The NGL.Component of the representations
     * name		 A string describing the group (used in GUI)
     * representations	 Optional list of representations to add
     * selection	 A selection of the subset of component atoms
     * 			 to which the representation is applied.
     * defaultParameters A dictionary of NGL.Representation parameters
     *			 to be applied to all representations.
     */
    selection = typeof selection === 'undefined' ? null : selection;
    representations = typeof representations === 'undefined' ? null : representations;
    defaultParameters = typeof defaultParameters === 'undefined' ? {} : defaultParameters;
    this.component = component;
    this.name = name;
    this.selection = selection;
    this.defaultParameters = defaultParameters;
    this.visible = false;
    
    this.reprList = [];

    var self = this;
    if(representations) 
        representations.forEach(function(repr) {
            self.addRepresentation(null, repr);
        });
};

RepresentationGroup.prototype.addRepresentation = function(display_name, repr, selection) {
    /*
     * Add a representation to this group.
     *
     * Arguments
     * ---------
     *
     * display_name	 For GUI
     * repr		 The RepresentationComponent (as returned by e.g. StructureComponent.addRepresentation)
     */
    selection = typeof selection === 'undefined' ? null : selection;
    repr.display_name = display_name;
    // Apply default parameters
    repr.setParameters(this.defaultParameters);
    // Set selection if defined
    if(selection)
        repr.setSelection(selection);
    else if(this.selection)
        repr.setSelection(this.selection);
    // Hide initially
    repr.setVisibility(false);
    this.reprList.push(repr);
    return this;
}
    
RepresentationGroup.prototype.toggle = function(checked) {
    // Toggle the visibility state of this group
    this.visible = checked;
    this.update();
}

RepresentationGroup.prototype.setVisibility = function(what) {
    // Show/Hide all representations in group
    this.reprList.forEach(function(repr) {
        repr.setVisibility(what);
    });
}
RepresentationGroup.prototype.setParameters = function(what) {
    // Show/Hide all representations in group
    this.reprList.forEach(function(repr) {
        repr.setParameters(what);
    });
}
RepresentationGroup.prototype.setSelection = function(what) {
    // Show/Hide all representations in group
    this.reprList.forEach(function(repr) {
        repr.setSelection(what);
    });
}

RepresentationGroup.prototype.update = function() {
    // Update representation visilibity
    this.setVisibility(this.visible);
}

RepresentationGroup.prototype.all_empty = function() {
    // Check if all representations in group are empty
    return this.reprList.every(function(repr) {
        return repr.repr.structureView.atomCount == 0;
    });
}

RepresentationGroup.prototype.GUI = function(class_name, enabled) {
    /*
     * Write HTML to control the visibility of this group.
     *
     * "class_name" is the class used for the container DIV.
     */
    enabled = typeof enabled === 'undefined' ? false : enabled;
    
    // If all the representation are empty, return empty GUI
    if(this.all_empty()) return null;
    
    var self = this,
        c = $("<div/>", {"class": class_name})

    // Enable first (only) representation
    this.enabled = 0;
    if(enabled) {
        this.toggle(true);
    }
    
    c.append($("<div/>").append(
        $("<input/>", {"type": "checkbox",
                       "id": class_name,
                       "checked": enabled})
            .click(function(e) {self.toggle(this.checked);}),
        $("<label/>", {"for": class_name}).append(this.name)
    ));
 
    if(this.reprList.length > 1) {
        var d = $("<div/>", {"class":"naradio"}),
            radioname = class_name+"radio";
        this.reprList.forEach(function(repr, i) {
            d.append($("<span/>").append(
                $("<input/>", {"type": "radio",
                               "name": radioname,
                               "id": radioname+"_"+i,
                               "checked": i==0})
                    .click(function(e) {self.enable(i);}),
                $("<label/>", {"for": radioname+"_"+i}).append(capitalize(repr.display_name)),
                "&nbsp;"
                ));
        });
        c.append(d);
    }
    this.update();
    return c;
}

/*
 *
 */

var MutuallyExclusiveRepresentationGroup = function(component, name, selection, representations, 
                                                    defaultParameters) {
    
    /* Mutually exclusive Representation Group
     * 
     * Define a group of mutually-exclusive representations, and enable writing
     * simple HTML GUI to control their visibility.  Which element is visible is
     * controlled by the "enabled" property. The global visibility of this group
     * is controlled by the "visible" property.
     *
     * Arguments
     * ---------
     *
     * component	 The NGL.Component of the representations
     * name		 A string describing the group (used in GUI)
     * representations	 Optional list of representations to add
     * selection	 A selection of the subset of component atoms
     * 			 to which the representation is applied.
     * defaultParameters A dictionary of NGL.Representation parameters
     *			 to be applied to all representations.
     */
    RepresentationGroup.call(this, component, name, selection, representations, defaultParameters);
    this.enabled = -1;
};

MutuallyExclusiveRepresentationGroup.prototype = Object.create(RepresentationGroup.prototype);

MutuallyExclusiveRepresentationGroup.prototype.enable = function(ci) {
    // Enable (only) one representation of the group, by index
    ci = typeof ci === 'undefined' ? -1 : ci;
    if(ci < this.reprList.length)
        this.enabled = ci;
    this.update();
}

MutuallyExclusiveRepresentationGroup.prototype.nenable = function(name) {
    /*
     * Enable a group by name
     */
    console.log("nen", name);
    var i = this.reprList.findIndex(function(repr) {
        return repr.name == name;
    });
    this.enable(i);
    return i;
}

MutuallyExclusiveRepresentationGroup.prototype.update = function() {
    // Update representation visilibity
    this.setVisibility(false);
    if(this.visible && this.enabled >= 0)
        this.reprList[this.enabled].setVisibility(true)
}
