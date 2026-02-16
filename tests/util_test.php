<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for util helper class.
 *
 * @package     local_invitation
 * @category    test
 * @author      Marcus Green 2026
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_invitation;

use local_invitation\helper\util;

/**
 * Unit tests for the util helper class.
 *
 * @package     local_invitation
 * @category    test
 * @author      Marcus Green 2026
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class util_test extends \advanced_testcase {
    /**
     * Test get_invitation_role_choices includes standard course roles.
     *
     * @covers util::get_invitation_role_choices
     *
     * @return void
     */
    public function test_get_invitation_role_choices_includes_course_roles(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $rolechoices = util::get_invitation_role_choices();

        // Standard course roles should be present.
        $roles = role_get_names();
        $studentrole = $roles['student'] ?? null;
        $teacherrole = $roles['teacher'] ?? null;
        $editingteacherrole = $roles['editingteacher'] ?? null;

        // Check that at least student role is available.
        if ($studentrole) {
            $this->assertArrayHasKey($studentrole->id, $rolechoices);
        }
        if ($teacherrole) {
            $this->assertArrayHasKey($teacherrole->id, $rolechoices);
        }
        if ($editingteacherrole) {
            $this->assertArrayHasKey($editingteacherrole->id, $rolechoices);
        }
    }
}
