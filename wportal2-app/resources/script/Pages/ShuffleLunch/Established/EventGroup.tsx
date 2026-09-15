import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { Box, Divider, Paper } from '@mui/material';
import { EEventTimeZone } from '@/script/Enum/Server/App/ShuffleLunch/EEventTimeZone';
import { useIndexContext } from '@/script/Pages/ShuffleLunch/Index';
import { responsiveSpacing } from '@/script/System/Responsive';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';
import { Avatar } from '@/script/Pages/Common/Avatar';

interface IProps extends IPropsBase {
  eventTimeZone: EEventTimeZone;
  groupId: number;
}
export const EventGroup: React.FC<IProps> = ({ eventTimeZone, groupId }) => {
  const { trnShuffleLunchGroupList } = useIndexContext();
  const filteredGroupList =
    trnShuffleLunchGroupList.getGroupListByEventTimeZoneAndGroupId(
      eventTimeZone,
      groupId,
    );

  return (
    <Paper
      key={groupId}
      sx={{
        padding: responsiveSpacing(4),
      }}
    >
      <TypoH3>{`グループ [${eventTimeZone}-${groupId}]`}</TypoH3>
      <Divider sx={{ marginTop: responsiveSpacing(2) }} />
      {filteredGroupList.map(group => {
        return (
          <Box
            key={group.id}
            sx={{
              marginTop: responsiveSpacing(2),
            }}
          >
            <Avatar trnUser={group.trnUser} />
          </Box>
        );
      })}
    </Paper>
  );
};
