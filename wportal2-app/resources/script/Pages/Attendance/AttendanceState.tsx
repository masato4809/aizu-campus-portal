import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAttendanceState } from '@/script/Pages/Attendance/IAttendanceState';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { Closable } from '@/script/Component/Misc/Closable';
import { DialogUser } from '@/script/Pages/Attendance/DialogUser';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  label: string;
  stateList: IAttendanceState[];
}
export const AttendanceState: React.FC<IProps> = ({ sx, label, stateList }) => {
  const [selectedUser, setSelectedUser] = React.useState<
    IAppTrnUser | undefined
  >(undefined);

  /**
   * ユーザーが選択された場合.
   */
  const handleSelect = (trnUser?: IAppTrnUser) => {
    setSelectedUser(trnUser);
  };

  return (
    <Box
      sx={{
        display: 'flex',
        borderBottom: '1px dashed',
        borderColor: 'divider',
        ...sx,
      }}
    >
      <Closable open={!!selectedUser}>
        <DialogUser
          trnUser={selectedUser}
          handleClose={() => setSelectedUser(undefined)}
        />
      </Closable>
      <TypoH2
        sx={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          minWidth: responsiveSize(120),
          padding: responsiveSpacing(2),
        }}
      >
        {label}
      </TypoH2>
      <TypoH2
        sx={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          minWidth: responsiveSize(80),
          padding: responsiveSpacing(2),
        }}
      >
        {stateList.length}
      </TypoH2>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
          flexWrap: 'wrap',
          py: responsiveSpacing(2),
          px: 0,
          gap: responsiveSpacing(2),
        }}
      >
        {stateList.map(state => {
          return (
            <Box key={state.trnUser.id}>
              <Avatar
                trnUser={state.trnUser}
                active={state.isWorking}
                rest={state.isRest}
                handleSelect={handleSelect}
              />
            </Box>
          );
        })}
      </Box>
    </Box>
  );
};
