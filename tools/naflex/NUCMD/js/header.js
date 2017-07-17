// JavaScript Document
            function toggleVis (a) {
                ob = document.getElementById(a);
                if (ob.style.display=='inline') {
                    ob.style.display = 'none';
                } else {
                    ob.style.display='inline';
                }
            }
            function submit(op) {
                document.searchForm.op.value=op;
                document.searchForm.submit();
            }
