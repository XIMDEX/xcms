/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

#include <sys/types.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <pwd.h>
#include <errno.h>
#include <libgen.h>

#define XIMDEX_USER "ximdex"

int main(int argc, char *argv[]) {

	uid_t caller_uid = getuid();
	uid_t caller_euid = geteuid();

	uid_t ximdex_uid;


	// Basename and path calc
	char *self_dir = dirname(argv[0]);
	char *exec_path = malloc(8192 * sizeof(char)); 

	strcpy(exec_path, self_dir);
	exec_path = strcat(exec_path, "/ximCRON.pl");

	// Open log file
	FILE *log_desc;
	char *log_path = malloc(8192 * sizeof(char));

	strcpy(log_path, self_dir);
	log_path = strcat(log_path, "/ximCRON.log");

	printf("[wrapper]: INFO : exec_str = %s | log_path = %s\n", exec_path, log_path);

	log_desc = fopen(log_path, "a");

	fprintf(log_desc, "[wrapper]: START - num args = %d [%s]\n", argc, exec_path);

	// Acquire ximdex user uid
	struct passwd *ximdex_passwd_struct;
	ximdex_passwd_struct = getpwnam(XIMDEX_USER);

	if ( ximdex_passwd_struct == NULL ) {
		fprintf(log_desc, "[wrapper]: ERROR : getpwnam('ximdex') [%s]\n", strerror(errno));
		fprintf(log_desc, "[wrapper]: Exiting. (user ximdex must exists)\n");
		exit(EXIT_FAILURE);
	}

	ximdex_uid = ximdex_passwd_struct->pw_uid;
	
	fprintf(log_desc, "[wrapper]: INFO : caller_uid = %d caller_euid = %d ximdex_uid = %d \n", caller_uid, caller_euid, ximdex_uid);

	// Inconditionally native setuid...
	
	if ( caller_uid == 0 || caller_euid == 0 ) {

		if ( setuid(ximdex_uid) < 0 ) {
			fprintf(log_desc, "[wrapper]: ERROR : setuid(%d) [%s] (exiting)\n", ximdex_uid, strerror(errno));
			exit(EXIT_FAILURE);
		}

	} else {
		fprintf(log_desc, "[wrapper]: INFO : we havent root permissions (not swapping to ximdex_user_id) setuid it!\n");
	}

	fprintf(log_desc, "[wrapper]: INFO : run_cmd_user_id: %d run_cmd_effective_user_id: %d\n", getuid(), geteuid());
	fprintf(log_desc, "[wrapper]: INFO : exec : %s \n", exec_path);

	// Close file
	fclose(log_desc);
	
	// Exec !
	int ret_exec;

	ret_exec = execvp(exec_path, argv);

	// If we reached end, exec was failed.
	if (ret_exec < 0) {
		fprintf(log_desc, "[wrapper]: ERROR : execvp [%s]\n", strerror(errno));
	}

	fprintf(log_desc, "[wrapper]: ERROR : return value from execl: %d\n", ret_exec);

	return EXIT_FAILURE;
}

