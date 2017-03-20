// Making active/non-active Menu Tabs
function menuTabs(name){
        var code = '#'+name+'Tab';
        var code2 = '#'+name+'LiTab';
        jQuery('a[id$="Tab"]')[0].className = "";
	if (typeof jQuery(code)[0]  != "undefined") {
        	jQuery(code)[0].className = "active";
	}
        //jQuery('a[id$="Tab"]')[0].className = "";
	if (typeof jQuery(code2)[0]  != "undefined") {
        	jQuery(code2)[0].className = "selected";
	}
        // Tinynav
        jQuery('#main-menu > ul.menu').tinyNav({
            active: 'selected', // Set the "active" class
        });
}

// Controlling form submission in Trajectories Generation (Download/NAFlex)
function submitform(type){
        var form = document.getElementById('metatraj');
        if(type != 'download') {
                form.type.value = 'naflex';
        }
        else {
                form.type.value = 'download';
        }

        if (form.mask.value == null || form.mask.value == "") {
                form.mask.style.backgroundColor = "yellow";
                alert("Mask must be filled out");
                return false;
        }
        if (form.frames.value == null || form.frames.value == "") {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be filled out");
                return false;
        }

        // Checking frames input
        fframes = form.frames.value;
        f = fframes.split(':');
        l = f.length;
        ini = f[0];
        end = f[1];
        step = f[2];
        total = end - ini;
        //alert("A: "+f[1]);
        if(l != 3) {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be like start:stop:step (ex: 0:20:1)");
                //return false;
                return;
        }

        totalSnaps = total / step;
        if(ini < 0 || end < 0 || step < 0) {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be positive (ex: 0:20:1)");
                //return false;
                return;
        }
        if(end < ini || step > total) {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be coherent (ex: initial snapshot greater than end snapshot, step number smaller than total number of snapshots)");
                //return false;
                return;
        }
//JL Pujo a 5000 per test
        if(totalSnaps > 5000) {
                form.frames.style.backgroundColor = "yellow";
                alert("Maximum number of frames exceeded (5000)");
                //return false;
                return;
        }

        // Checking mask input
        mask = form.mask.value;

        form.submit();
}

// Controlling form submission in Trajectories Generation (Download/NAFlex)
function submitformMetaTraj(type,naflexLen){
        var form = document.getElementById('metatrajFragment');
        if(type != 'download') {
                form.type.value = 'naflex';
		//var cont = confirm('WARNING: we strongly recommend to first download the meta-trajectory and then send it to NAFlex when the trajectory is going to be large. With large trajectories, this process can take several minutes. \n\nAre you sure you want to continue with the direct redirection?');
		//if (!cont) { return; }
        }
        else {
                form.type.value = 'download';
        }

        if (form.frames.value == null || form.frames.value == "") {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be filled out");
                return false;
        }

        // Checking frames input
        fframes = form.frames.value;
        f = fframes.split(':');
        l = f.length;
        ini = f[0];
        end = f[1];
        step = f[2];
        total = end - ini;
        //alert("A: "+f[1]);
        if(l != 3) {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be like start:stop:step (ex: 0:20:1)");
                //return false;
                return;
        }

        totalSnaps = total / step;
        if(ini < 0 || end < 0 || step < 0) {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be positive (ex: 0:20:1)");
                //return false;
                return;
        }
        if(end < ini || step > total) {
                form.frames.style.backgroundColor = "yellow";
                alert("Frames must be coherent (ex: initial snapshot greater than end snapshot, step number smaller than total number of snapshots)");
                //return false;
                return;
        }
//JL Pujo a 5000 per test
        if(totalSnaps > 5000) {
                form.frames.style.backgroundColor = "yellow";
                alert("Maximum number of frames exceeded (5000)");
                //return false;
                return;
        }

	naflexCutoff = naflexLen * totalSnaps;
	if( (type != 'download') && (naflexCutoff > 500)) {
		alert('INFO: as the generation of this meta-trajectory will take several minutes, BIGNASim will first download the meta-trajectory to a session workspace. \n\nTrajectory will appear in that workspace once generated, and from that, it could be sent to NAFlex.\n\n');
		form.type.value = 'download';
	}

        form.submit();
}

