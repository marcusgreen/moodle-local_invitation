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

    /**
     * Test get_invitation_systemrole_choices includes a "none" option and system roles.
     *
     * @covers util::get_invitation_systemrole_choices
     *
     * @return void
     */
    public function test_get_invitation_systemrole_choices(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $rolechoices = util::get_invitation_systemrole_choices();

        // The "none" option (key 0) must always be present.
        $this->assertArrayHasKey(0, $rolechoices);

        // Standard system-assignable roles should be present (manager is assignable at system level by default).
        $roles = role_get_names();
        if (!empty($roles['manager'])) {
            $this->assertArrayHasKey($roles['manager']->id, $rolechoices);
        }
    }

    /**
     * Test that create_invitation and update_invitation persist the systemrole.
     *
     * @covers util::create_invitation
     * @covers util::update_invitation
     *
     * @return void
     */
    public function test_invitation_persists_systemrole(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $roles = role_get_names();
        $studentid = $roles['student']->id;
        $managerid = $roles['manager']->id;

        // Create an invitation with a system role.
        $invitedata = new \stdClass();
        $invitedata->courseid   = $course->id;
        $invitedata->title      = 'Test invitation';
        $invitedata->userrole   = $studentid;
        $invitedata->systemrole = $managerid;
        $invitedata->maxusers   = 5;
        $invitedata->timestart  = time();
        $invitedata->timeend    = time() + DAYSECS;

        $id = util::create_invitation($invitedata);
        $this->assertNotEmpty($id);

        $record = $DB->get_record('local_invitation', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals($managerid, $record->systemrole);

        // Update the invitation to clear the system role (none).
        $invitedata->systemrole = 0;
        $this->assertTrue(util::update_invitation($record, $invitedata));

        $record = $DB->get_record('local_invitation', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals(0, $record->systemrole);
    }
}
