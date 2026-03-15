<?php
if (is_dir("../img/staff/")) {
    echo "Directory exists\n";
} else {
    echo "Directory not found\n";
    echo realpath("..");
}
